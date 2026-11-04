<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiJob extends Model
{
    protected $table = 'api_jobs';

    protected $fillable = [
        'type',
        'status',
        'progress',
        'message',
        'data',
        'result',
        'error',
    ];

    protected $casts = [
        'data' => 'array',
        'result' => 'array',
        'progress' => 'integer',
    ];

    /**
     * Shape response yang diminta frontend (/api/v1/jobs/{id}).
     */
    public function toApiResponse(): array
    {
        $result = $this->result ?? [];

        return [
            'id' => $this->id,
            'job_id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'progress' => (int) ($this->progress ?? 0),
            'message' => $this->message,
            'error' => $this->error,
            'preview_url' => $result['preview_url'] ?? null,
            'clip_url' => $result['clip_url'] ?? null,
            'result_url' => $result['result_url'] ?? ($result['clip_url'] ?? null),
            'download_url' => $result['download_url'] ?? null,
            'duration' => $result['duration'] ?? null,
            'segments' => $result['segments'] ?? null,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
