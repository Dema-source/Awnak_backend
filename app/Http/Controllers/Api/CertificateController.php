<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Certificate\StoreCertificateRequest;
use App\Http\Requests\Api\Certificate\UpdateCertificateRequest;
use App\Http\Resources\CertificateResource;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * CertificateController Constructor.
     *
     * @param CertificateService $service.
     */
    public function __construct(
        protected CertificateService $service
    ) {}

    /**
     * Display a paginated listing of Certificates.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Certificate list fetched successfully');
    }

    /**
     * Store a newly created Certificate in storage.
     *
     * @param StoreCertificateRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreCertificateRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success(new CertificateResource($item), 'Certificate created successfully');
    }

    /**
     * Display the specified Certificate.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new CertificateResource($item), 'Certificate fetched successfully');
    }

    /**
     * Update the specified Certificate in storage.
     * 
     * @param UpdateCertificateRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateCertificateRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(new CertificateResource($item), 'Certificate updated successfully');
    }

    /**
     * Remove the specified Certificate from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Certificate deleted successfully');
    }
}