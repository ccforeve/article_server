<?php

namespace App\Observers;

use App\Models\Collector;

class CollectorObserver
{
    /**
     * Handle the article "deleted" event.
     *
     * @param  \App\Models\Collector  $collector
     * @return void
     */
    public function deleted(Collector $collector)
    {
        $collector->collections()->delete();
    }
}
