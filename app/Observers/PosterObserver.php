<?php

namespace App\Observers;

use App\Models\Poster;

class PosterObserver
{
    /**
     * Handle the poster "deleted" event.
     *
     * @param  \App\Models\Poster  $poster
     * @return void
     */
    public function deleting(Poster $poster)
    {
        info('poster', $poster);
        info('image_url', cdn_path("uploads{$poster->image_url}"));
        unlink(cdn_path("uploads{$poster->image_url}"));
    }
}
