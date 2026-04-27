<?php

namespace App\Repositories\Eloquent;

use App\Models\Document;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class DocumentRepository implements DocumentRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Document $model  
     */ 
    public function __construct(
        protected Document $model
    ) {}

    /**  
     * Get a paginated list of records applying optional filters.  
     *  
     * @param array $filters Key/value filters to apply to the query.  
     * @param int $perPage Number of items per page.  
     * @return LengthAwarePaginator  
     */  
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->filter($filters)->with('documentable')->latest()->paginate($perPage);
    }

    /**  
     * Retrieve a single record by ID or throw an exception if not found.  
     *  
     * @param int|string $id  
     * @return Document  
     */ 
    public function findById(int|string $id): Document
    {
        return $this->model->with('documentable')->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return Document  
     */
    public function create(array $data): Document
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Document
     */
    public function update(int|string $id, array $data): Document
    {
        $item = $this->findById($id);
        $item->update($data);

        return $item->fresh();
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $item = $this->findById($id);

        return (bool) $item->delete();
    }

    /**
     * Get documents by user ID.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByVolunteer(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->byVolunteer($userId)->with('documentable')->latest()->paginate($perPage);
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
        return $this->model->byOrganization($organizationId)->with('documentable')->latest()->paginate($perPage);
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
        $filters = array_merge($filters, ['type' => $type]);
        return $this->model->filter($filters)->with('documentable')->latest()->paginate($perPage);
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
        $filters = array_merge($filters, ['search' => $searchTerm]);
        return $this->model->filter($filters)->with('documentable')->latest()->paginate($perPage);
    }

    /**
     * Search documents by title and/or type.
     *
     * @param string|null $searchTerm
     * @param string|null $type
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(?string $searchTerm = null, ?string $type = null, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        if ($searchTerm) {
            $filters['search'] = $searchTerm;
        }
        if ($type) {
            $filters['type'] = $type;
        }
        
        return $this->model->filter($filters)->with('documentable')->latest()->paginate($perPage);
    }

    /**
     * Get file content for download.
     *
     * @param int|string $id
     * @return array
     */
    public function getFileForDownload(int|string $id): array
    {
        $document = $this->findById($id);
        
        if (!Storage::disk('public')->exists($document->path)) {
            throw new \Exception('File not found');
        }

        $fileContent = Storage::disk('public')->get($document->path);
        $fileName = $document->title . '.' . pathinfo($document->path, PATHINFO_EXTENSION);
        $mimeType = $this->getMimeType($document->path);

        return [
            'content' => $fileContent,
            'filename' => $fileName,
            'mime_type' => $mimeType,
            'size' => strlen($fileContent)
        ];
    }

    /**
     * Get file content for inline display.
     *
     * @param int|string $id
     * @return array
     */
    public function getFileForRead(int|string $id): array
    {
        $document = $this->findById($id);
        
        if (!Storage::disk('public')->exists($document->path)) {
            throw new \Exception('File not found');
        }

        $fileContent = Storage::disk('public')->get($document->path);
        $mimeType = $this->getMimeType($document->path);

        return [
            'content' => $fileContent,
            'filename' => $document->title,
            'mime_type' => $mimeType,
            'size' => strlen($fileContent)
        ];
    }

    /**
     * Get file URL and metadata.
     *
     * @param int|string $id
     * @return array
     */
    public function getFileUrl(int|string $id): array
    {
        $document = $this->findById($id);
        
        if (!\Storage::disk('public')->exists($document->path)) {
            throw new \Exception('File not found');
        }

        $url = asset('storage/' . $document->path);
        $mimeType = $this->getMimeType($document->path);

        return [
            'url' => $url,
            'filename' => $document->title,
            'type' => $document->type,
            'mime_type' => $mimeType
        ];
    }

    /**
     * Get MIME type for a file.
     *
     * @param string $filePath
     * @return string
     */
    private function getMimeType(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'text' => 'text/plain',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
