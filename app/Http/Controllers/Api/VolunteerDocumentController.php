<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Document\StoreDocumentRequest;
use App\Http\Requests\Api\Document\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Display a paginated listing of volunteer documents.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        // Filter to only show volunteer documents for the authenticated user
        $user = Auth::user();
        $filters['documentable_type'] = 'App\Models\Volunteer';
        
        // Get volunteer record for this user
        $volunteer = \App\Models\Volunteer::whereHas('profile', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->first();
        
        if ($volunteer) {
            $filters['documentable_id'] = $volunteer->id;
        }

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Volunteer documents fetched successfully');
    }

    /**
     * Store a newly created volunteer document in storage.
     *
     * @param StoreDocumentRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Get volunteer record for authenticated user
        $user = Auth::user();
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
     * Display the specified volunteer document.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);
        
        // Check authorization - only user can access their own volunteer documents
        $this->authorizeVolunteerDocumentAccess($item);

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
        $item = $this->service->findById($id);
        
        // Check authorization - only user can update their own volunteer documents
        $this->authorizeVolunteerDocumentAccess($item);

        $updatedItem = $this->service->update($id, $request->validated());

        return $this->success(new DocumentResource($updatedItem), 'Volunteer document updated successfully');
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
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getMyDocuments(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $user = Auth::user();
        
        // Get volunteer record for this user
        $volunteer = \App\Models\Volunteer::whereHas('profile', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->first();
        
        if (!$volunteer) {
            return $this->error('Volunteer profile not found', 404);
        }
        
        $filters['documentable_type'] = 'App\Models\Volunteer';
        $filters['documentable_id'] = $volunteer->id;

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'My volunteer documents fetched successfully');
    }

    /**
     * Get volunteer documents by type.
     *
     * @param string $type The document type.
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getByType(string $type, Request $request): JsonResponse
    {
        $filters = array_merge($request->except(['page', 'per_page']), [
            'type' => $type,
            'documentable_type' => 'App\Models\Volunteer'
        ]);
        $perPage = (int) $request->input('per_page', 15);

        $user = Auth::user();
        
        // Get volunteer record for this user
        $volunteer = \App\Models\Volunteer::whereHas('profile', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->first();
        
        if ($volunteer) {
            $filters['documentable_id'] = $volunteer->id;
        }

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, "Volunteer documents of type '{$type}' fetched successfully");
    }

    /**
     * Search volunteer documents by title.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $searchTerm = $request->input('search', '');
        $filters = $request->except(['page', 'per_page', 'search']);
        $perPage = (int) $request->input('per_page', 15);

        $user = Auth::user();
        
        // Get volunteer record for this user
        $volunteer = \App\Models\Volunteer::whereHas('profile', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->first();
        
        $filters['documentable_type'] = 'App\Models\Volunteer';
        if ($volunteer) {
            $filters['documentable_id'] = $volunteer->id;
        }
        $filters['search'] = $searchTerm;

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, "Volunteer documents matching '{$searchTerm}' fetched successfully");
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
        if ($document->documentable_type !== 'App\Models\Volunteer' || 
            $document->documentable->profile->user_id !== $user->id) {
            abort(403, 'You can only access your own volunteer documents');
        }
    }
}
