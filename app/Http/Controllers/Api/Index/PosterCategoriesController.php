<?php

namespace App\Http\Controllers\Api\Index;

use App\Services\PosterCategoryService;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class PosterCategoriesController extends Controller
{
    public function list( PosterCategoryService $service )
    {
        return $service->list();
    }
}
