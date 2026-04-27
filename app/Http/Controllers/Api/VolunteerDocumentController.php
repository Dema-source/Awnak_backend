<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Document\StoreDocumentRequest;
use App\Http\Requests\Api\Document\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VolunteerDocumentController extends Controller
{
    /**
     * VolunteerDocumentController Constructor.
     *
     * @param DocumentService $service.
     */
    public function __construct(
        protected DocumentService $service
    ) {}

    /**
     * Display a paginated listing of all volunteer documents (Super Admin and System Admin).
     * Supports search by title and type.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $searchTerm = $request->input('search');
        $type = $request->input('type');
        $filters = $request->except(['page', 'per_page', 'search', 'type']);
        $perPage = (int) $request->input('per_page', 15);

        // Filter to only show volunteer documents
        $filters['documentable_type'] = 'App\Models\Volunteer';

        // Don't filter by documentable_id to see all volunteer documents
        // This method is for super admins only

        // Use search if search term or type is provided
        if ($searchTerm || $type) {
            $data = $this->service->search($searchTerm, $type, $filters, $perPage);
        } else {
            $data = $this->service->getAll($filters, $perPage);
        }

        return $this->paginate($data, 'All volunteer documents fetched successfully');
    }

    /**
     * Store a newly created volunteer document in storage.
     *
     * @param StoreDocumentRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $user = Auth::user();

        // Route to appropriate method based on user role
        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return $this->storeForAdmin($request);
        } else {
            return $this->storeForUser($request);
        }
    }

    /**
     * Display the specified volunteer document.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        // Volunteer documents are publicly visible for organizations to review volunteers
        // No authorization check needed - these are like public profiles/certificates

        return $this->success(new DocumentResource($item), 'Volunteer document fetched successfully');
    }

    /**
     * Update the specified volunteer document in storage.
     * 
     * @param UpdateDocumentRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateDocumentRequest $request, int|string $id): JsonResponse
    {
        $user = Auth::user();

        // Route to appropriate method based on user role
        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return $this->updateForAdmin($request, $id);
        } else {
            return $this->updateForUser($request, $id);
        }
    }

    /**
     * Remove the specified volunteer document from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        // Check authorization - only user can delete their own volunteer documents
        $this->authorizeVolunteerDocumentAccess($item);

        $this->service->delete($id);

        return $this->success(null, 'Volunteer document deleted successfully');
    }

    /**
     * Get documents for the authenticated volunteer.
     * Supports search by title and type.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getMyDocuments(Request $request): JsonResponse
    {
        $searchTerm = $request->input('search');
        $type = $request->input('type');
        $filters = $request->except(['page', 'per_page', 'search', 'type']);
        $perPage = (int) $request->input('per_page', 15);

        // Filter to only show volunteer documents for authenticated user
        $user = Auth::user();
        $filters['documentable_type'] = 'App\Models\Volunteer';

        // Get volunteer record for this user
        $volunteer = \App\Models\Volunteer::whereHas('profile', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->first();

        if ($volunteer) {
            $filters['documentable_id'] = $volunteer->id;
        }

        // Use search if search term or type is provided
        if ($searchTerm || $type) {
            $data = $this->service->search($searchTerm, $type, $filters, $perPage);
        } else {
            $data = $this->service->getAll($filters, $perPage);
        }

        return $this->paginate($data, 'My volunteer documents fetched successfully');
    }

    /**
     * Store document for regular volunteer (own record only).
     *
     * @param StoreDocumentRequest $request
     * @return JsonResponse
     */
    private function storeForUser(StoreDocumentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        // Get volunteer record for authenticated user
        $volunteer = \App\Models\Volunteer::whereHas('profile', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->first();

        if (!$volunteer) {
            return $this->error('Volunteer profile not found', 404);
        }

        // Set documentable to volunteer's record
        $data['documentable_type'] = 'App\Models\Volunteer';
        $data['documentable_id'] = $volunteer->id;

        $item = $this->service->create($data);

        return $this->success(new DocumentResource($item), 'Volunteer document uploaded successfully');
    }

    /**
     * Store document for admin (any volunteer).
     *
     * @param StoreDocumentRequest $request
     * @return JsonResponse
     */
    private function storeForAdmin(StoreDocumentRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Check if documentable_id is provided for specific volunteer
        if ($request->has('documentable_id')) {
            $volunteer = \App\Models\Volunteer::find($request->input('documentable_id'));
            if (!$volunteer) {
                return $this->error('Volunteer not found', 404);
            }
            $data['documentable_type'] = 'App\Models\Volunteer';
            $data['documentable_id'] = $volunteer->id;
        } else {
            return $this->error('documentable_id is required for admin uploads', 422);
        }

        $item = $this->service->create($data);

        return $this->success(new DocumentResource($item), 'Volunteer document uploaded successfully');
    }

    /**
     * Update document for regular volunteer (own record only).
     *
     * @param UpdateDocumentRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    private function updateForUser(UpdateDocumentRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        // Check authorization - only user can update their own volunteer documents
        $this->authorizeVolunteerDocumentAccess($item);

        $updatedItem = $this->service->update($id, $request->validated());

        return $this->success(new DocumentResource($updatedItem), 'Volunteer document updated successfully');
    }

    /**
     * Update document for admin (any volunteer document).
     *
     * @param UpdateDocumentRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    private function updateForAdmin(UpdateDocumentRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);
        $data = $request->validated();

        // Super admins can update any volunteer document
        if ($item->documentable_type !== 'App\Models\Volunteer') {
            return $this->error('This is not a volunteer document', 403);
        }

        // Handle documentable changes if provided
        if ($request->has('documentable_id')) {
            $volunteer = \App\Models\Volunteer::find($request->input('documentable_id'));
            if (!$volunteer) {
                return $this->error('Volunteer not found', 404);
            }
            $data['documentable_type'] = 'App\Models\Volunteer';
            $data['documentable_id'] = $volunteer->id;
        }

        $updatedItem = $this->service->update($id, $data);

        return $this->success(new DocumentResource($updatedItem), 'Volunteer document updated successfully');
    }

    /**
     * Download volunteer document file.
     *
     * @param int|string $id The document ID.
     * @return Response
     */
    public function download(int|string $id): Response
    {
        $document = $this->service->findById($id);

        // Verify this is a volunteer document
        if ($document->documentable_type !== 'App\Models\Volunteer') {
            abort(404, 'Document not found');
        }

        // Check authorization
        // $this->authorizeVolunteerDocumentAccess($document);

        try {
            $fileData = $this->service->getFileForDownload($id);
        } catch (\Exception $e) {
            abort(404, 'File not found');
        }

        return response($fileData['content'])
            ->header('Content-Type', $fileData['mime_type'])
            ->header('Content-Disposition', 'attachment; filename="' . $fileData['filename'] . '"')
            ->header('Content-Length', $fileData['size']);
    }

    /**
     * Read/view volunteer document file (inline display).
     *
     * @param int|string $id The document ID.
     * @return Response
     */
    public function read(int|string $id): Response
    {
        $document = $this->service->findById($id);

        // Verify this is a volunteer document
        if ($document->documentable_type !== 'App\Models\Volunteer') {
            abort(404, 'Document not found');
        }

        // Check authorization
        // $this->authorizeVolunteerDocumentAccess($document);

        try {
            $fileData = $this->service->getFileForRead($id);
        } catch (\Exception $e) {
            abort(404, 'File not found');
        }

        return response($fileData['content'])
            ->header('Content-Type', $fileData['mime_type'])
            ->header('Content-Disposition', 'inline; filename="' . $fileData['filename'] . '"')
            ->header('Content-Length', $fileData['size']);
    }

    /**
     * Get file URL for volunteer document.
     *
     * @param int|string $id The document ID.
     * @return JsonResponse
     */
    public function getFileUrl(int|string $id): JsonResponse
    {
        $document = $this->service->findById($id);

        // Verify this is a volunteer document
        if ($document->documentable_type !== 'App\Models\Volunteer') {
            abort(404, 'Document not found');
        }

        // Check authorization
        $this->authorizeVolunteerDocumentAccess($document);

        try {
            $fileData = $this->service->getFileUrl($id);
        } catch (\Exception $e) {
            return $this->error('File not found', 404);
        }

        return $this->success($fileData, 'File URL generated successfully');
    }

    
    /**
     * Authorize user to access volunteer document.
     *
     * @param \App\Models\Document $document
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function authorizeVolunteerDocumentAccess($document): void
    {
        $user = Auth::user();

        // Super admins and system admins can access all documents
        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return;
        }

        // Only volunteers can access volunteer documents
        if (!$user->hasRole('volunteer')) {
            abort(403, 'Only volunteers can access volunteer documents');
        }

        // Volunteers can only access their own documents
        if (
            $document->documentable_type !== 'App\Models\Volunteer' ||
            $document->documentable->profile->user_id !== $user->id
        ) {
            abort(403, 'You can only access your own volunteer documents');
        }
    }
}
