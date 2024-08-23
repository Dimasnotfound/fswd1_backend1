<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public $resource;
    public $success;
    public $message;
    public $status;

    public function __construct($resource, $success = true, $message = 'Operation successful', $status = 200)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->success = $success;
        $this->message = $message;
        $this->status = $status;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->resource,
            'code' => $this->status,
        ];
    }
}
