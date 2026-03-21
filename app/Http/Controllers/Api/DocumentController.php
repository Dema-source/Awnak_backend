<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Document\StoreDocumentRequest;
use App\Http\Requests\Api\Document\UpdateDocumentRequest;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * DocumentController Constructor.
     *
     * @param DocumentService $service.
     */
    public function __construct(
        protected DocumentService $service
    ) {}

    /**
     * Display a paginated listing of Documents.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Document list fetched successfully');
    }

    /**
     * Store a newly created Document in storage.
     *
     * @param StoreDocumentRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success($item, 'Document created successfully');
    }

    /**
     * Display the specified Document.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'Document fetched successfully');
    }

    /**
     * Update the specified Document in storage.
     * 
     * @param UpdateDocumentRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateDocumentRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'Document updated successfully');
    }

    /**
     * Remove the specified Document from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Document deleted successfully');
    }
}