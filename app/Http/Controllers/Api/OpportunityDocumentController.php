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

class OpportunityDocumentController extends Controller
{
    /**
     * OpportunityDocumentController Constructor.
     *
     * @param DocumentService $service.
     */
    public function __construct(
        protected DocumentService $service
    ) {}

    /**
     * Display a paginated listing of opportunity documents.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        // Filter to only show opportunity documents for the authenticated organization
        $user = Auth::user();
        $filters['documentable_type'] = 'App\Models\Opportunity';
        
        // Get organization profile for this user
        $organizationId = $user->organization_profile?->id;
        
        if ($organizationId) {
            // Filter by organization's opportunities
            $filters['organization_id'] = $organizationId;
        }

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Opportunity documents fetched successfully');
    }

    /**
     * Store a newly created opportunity document in storage.
     *
     * @param StoreDocumentRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Validate that the opportunity belongs to the authenticated organization
        $user = Auth::user();
        $organizationId = $user->organization_profile?->id;
        
        if (!$organizationId) {
            return $this->error('Organization profile not found', 404);
        }
        
        // Verify the opportunity belongs to this organization
        $opportunity = \App\Models\Opportunity::where('id', $data['documentable_id'])
            ->where('organization_profile_id', $organizationId)
            ->first();
            
        if (!$opportunity) {
            return $this->error('Opportunity not found or does not belong to your organization', 403);
        }
        
        // Set documentable to opportunity
        $data['documentable_type'] = 'App\Models\Opportunity';

        $item = $this->service->create($data);

        return $this->success(new DocumentResource($item), 'Opportunity document uploaded successfully');
    }

    /**
     * Display the specified opportunity document.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);
        
        // Check authorization - only organization can access their opportunity documents
        $this->authorizeOpportunityDocumentAccess($item);

        return $this->success(new DocumentResource($item), 'Opportunity document fetched successfully');
    }

    /**
     * Update the specified opportunity document in storage.
     * 
     * @param UpdateDocumentRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateDocumentRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);
        
        // Check authorization - only organization can update their opportunity documents
        $this->authorizeOpportunityDocumentAccess($item);

        $updatedItem = $this->service->update($id, $request->validated());

        return $this->success(new DocumentResource($updatedItem), 'Opportunity document updated successfully');
    }

    /**
     * Remove the specified opportunity document from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);
        
        // Check authorization - only organization can delete their opportunity documents
        $this->authorizeOpportunityDocumentAccess($item);

        $this->service->delete($id);

        return $this->success(null, 'Opportunity document deleted successfully');
    }

    /**
     * Get documents for the authenticated organization's opportunities.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getMyOpportunityDocuments(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $user = Auth::user();
        $organizationId = $user->organization_profile?->id;
        
        if (!$organizationId) {
            return $this->error('Organization profile not found', 404);
        }
        
        $filters['documentable_type'] = 'App\Models\Opportunity';
        $filters['organization_id'] = $organizationId;

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'My opportunity documents fetched successfully');
    }

    /**
     * Get opportunity documents by type.
     *
     * @param string $type The document type.
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getByType(string $type, Request $request): JsonResponse
    {
        $filters = array_merge($request->except(['page', 'per_page']), [
            'type' => $type,
            'documentable_type' => 'App\Models\Opportunity'
        ]);
        $perPage = (int) $request->input('per_page', 15);

        $user = Auth::user();
        $organizationId = $user->organization_profile?->id;
        
        if ($organizationId) {
            $filters['organization_id'] = $organizationId;
        }

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, "Opportunity documents of type '{$type}' fetched successfully");
    }

    /**
     * Get documents for a specific opportunity.
     *
     * @param int $opportunityId The opportunity ID.
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getByOpportunity(int $opportunityId, Request $request): JsonResponse
    {
        $filters = array_merge($request->except(['page', 'per_page']), [
            'documentable_type' => 'App\Models\Opportunity',
            'documentable_id' => $opportunityId
        ]);
        $perPage = (int) $request->input('per_page', 15);

        // Verify the opportunity belongs to the authenticated organization
        $user = Auth::user();
        $organizationId = $user->organization_profile?->id;
        
        $opportunity = \App\Models\Opportunity::where('id', $opportunityId)
            ->where('organization_profile_id', $organizationId)
            ->first();
            
        if (!$opportunity) {
            return $this->error('Opportunity not found or does not belong to your organization', 403);
        }

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, "Documents for opportunity {$opportunityId} fetched successfully");
    }

    /**
     * Search opportunity documents by title.
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
        $organizationId = $user->organization_profile?->id;
        
        $filters['documentable_type'] = 'App\Models\Opportunity';
        if ($organizationId) {
            $filters['organization_id'] = $organizationId;
        }
        $filters['search'] = $searchTerm;

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, "Opportunity documents matching '{$searchTerm}' fetched successfully");
    }

    /**
     * Authorize user to access opportunity document.
     *
     * @param \App\Models\Document $document
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function authorizeOpportunityDocumentAccess($document): void
    {
        $user = Auth::user();

        // Super admins and system admins can access all documents
        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return;
        }

        // Only organization admins can access opportunity documents
        if (!$user->hasRole('organization_admin')) {
            abort(403, 'Only organization admins can access opportunity documents');
        }

        // Organizations can only access their own opportunity documents
        if ($document->documentable_type !== 'App\Models\Opportunity' || 
            $document->documentable->organization_profile_id !== $user->organization_profile?->id) {
            abort(403, 'You can only access your organization\'s opportunity documents');
        }
    }
}
