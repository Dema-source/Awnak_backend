<?php

namespace App\Services;

use App\Repositories\Interfaces\DocumentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

/**
 * Service layer for handling business logic related to the "DocumentRepositoryInterface" repository.
 */
class DocumentService
{
    /**
     * DocumentService Constructor.
     *
     * @param \App\Repositories\Interfaces\DocumentRepositoryInterface $repository
     */
    public function __construct(
        protected DocumentRepositoryInterface $repository
    ) {}

    /**
     * Retrieve a paginated list of records applying optional dynamic filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    /**
     * Find a record by its ID.
     *
     * @param int|string $id
     * @return mixed
     */
    public function findById(int|string $id): mixed
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new record using the provided data.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        // Handle file upload if present
        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            $file = $data['file'];
            $data['path'] = $this->uploadFile($file);
            $data['type'] = $this->getFileType($file);
            $data['original_name'] = $file->getClientOriginalName();
            unset($data['file']);
        }

        return $this->repository->create($data);
    }

    /**
     * Update an existing record by ID with the given data.
     *
     * @param int|string $id
     * @param array $data
     * @return mixed
     */
    public function update(int|string $id, array $data): mixed
    {
        // Handle file upload if present
        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            $file = $data['file'];
            $data['path'] = $this->uploadFile($file);
            $data['type'] = $this->getFileType($file);
            $data['original_name'] = $file->getClientOriginalName();
            
            // Delete old file
            $oldDocument = $this->findById($id);
            $this->deleteFile($oldDocument->path);
            unset($data['file']);
        }

        return $this->repository->update($id, $data);
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $document = $this->findById($id);
        
        // Delete physical file
        $this->deleteFile($document->path);
        
        return $this->repository->delete($id);
    }

    /**
     * Upload file to storage.
     *
     * @param UploadedFile $file
     * @return string
     */
    private function uploadFile(UploadedFile $file): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $filename, 'public');
        
        return $path;
    }

    /**
     * Get file type based on MIME type.
     *
     * @param UploadedFile $file
     * @return string
     */
    private function getFileType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();
        
        // Map common MIME types to document types
        $mimeTypes = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'image/jpeg' => 'image',
            'image/png' => 'image',
            'image/gif' => 'image',
            'text/plain' => 'text',
        ];

        return $mimeTypes[$mimeType] ?? 'other';
    }

    /**
     * Delete physical file from storage.
     *
     * @param string $path
     * @return bool
     */
    private function deleteFile(string $path): bool
    {
        if (\Storage::disk('public')->exists($path)) {
            return \Storage::disk('public')->delete($path);
        }
        
        return true;
    }

    /**
     * Get documents by user ID.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByUser($userId, $perPage);
    }

    /**
     * Get documents by organization ID.
     *
     * @param int $organizationId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByOrganization($organizationId, $perPage);
    }

    /**
     * Get documents by type.
     *
     * @param string $type
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByType(string $type, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByType($type, $filters, $perPage);
    }

    /**
     * Search documents by title.
     *
     * @param string $searchTerm
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchByTitle(string $searchTerm, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->searchByTitle($searchTerm, $filters, $perPage);
    }
}
