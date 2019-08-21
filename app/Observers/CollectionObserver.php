<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\Collection;
use App\Models\Footprint;
use App\Models\UserArticle;

class CollectionObserver
{
    /**
     * Handle the article "deleted" event.
     *
     * @param  \App\Models\Collection  $collection
     * @return void
     */
    public function created(Collection $collection)
    {
        $collection->collector()->update(['updated_at' => now()]);
    }
}
