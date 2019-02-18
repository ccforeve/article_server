<?php

namespace App\Http\Controllers\Api\Index;

use App\Models\ArticleCategory;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class ArticleCategoriesController extends Controller
{
    public function list( Request $request )
    {
        $categories = ArticleCategory::query()->latest('sort')->get();
        $categories->transform(function ($cate, $key) {
            $value = collect($cate);
            $value->put('aaa', 'mescrollInit' . $key);

            return $value;
        });

        return $categories;
    }
}
