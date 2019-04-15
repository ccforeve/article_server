<?php

namespace App\Jobs;

use App\Models\Poster;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CachePosters implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     */
    public function __construct()
    {

    }

    /**
     * 推送推广消息和图片
     */
    public function handle()
    {
        $posters = Poster::query()->get(['id', 'image_url', 'title']);
        foreach ($posters as $key => $poster) {
            imgChangeBase64($poster->image_url, "{$poster->id}_{$poster->title}");
        }
    }
}
