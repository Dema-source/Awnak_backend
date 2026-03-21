<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Evaluation\StoreEvaluationRequest;
use App\Http\Requests\Api\Evaluation\UpdateEvaluationRequest;
use App\Services\EvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    /**
     * EvaluationController Constructor.
     *
     * @param EvaluationService $service.
     */
    public function __construct(
        protected EvaluationService $service
    ) {}

    /**
     * Display a paginated listing of Evaluations.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Evaluation list fetched successfully');
    }

    /**
     * Store a newly created Evaluation in storage.
     *
     * @param StoreEvaluationRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreEvaluationRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success($item, 'Evaluation created successfully');
    }

    /**
     * Display the specified Evaluation.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'Evaluation fetched successfully');
    }

    /**
     * Update the specified Evaluation in storage.
     * 
     * @param UpdateEvaluationRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateEvaluationRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'Evaluation updated successfully');
    }

    /**
     * Remove the specified Evaluation from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Evaluation deleted successfully');
    }
}