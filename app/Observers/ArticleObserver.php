<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\Footprint;
use App\Models\UserArticle;

class ArticleObserver
{
    /**
     * Handle the article "deleted" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function deleted(Article $article)
    {
        $article->userArticle()->delete();

        $article->footprint()->delete();
    }
}
