<?php

namespace App\Http\Controllers\Api\Index;

use App\Jobs\InvitationPoster;
use App\Models\Poster;
use App\Services\PosterService;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class PostersController extends Controller
{
    /**
     * 分类海报
     * @param PosterService $service
     * @param $category_id
     * @return mixed
     */
    public function catePoster( PosterService $service, $category_id )
    {
        $posters = $service->catePoster($category_id);

        return $posters;
    }

    /**
     * 品牌海报
     * @param PosterService $service
     * @param $brand_id
     * @return mixed
     */
    public function BrandPoster( PosterService $service, $brand_id )
    {
        $posters = $service->brandPoster($brand_id);

        return $posters;
    }

    public function show( Poster $poster )
    {
        $base64 = imgChangeBase64($poster->image_url);
        return $this->response->array([
            'id' => $poster->id,
            'image_url' => $base64,
            'poster_type' => $poster->poster_type,
            'poster_id' => $poster->poster_id
        ]);
    }

    /**
     * 随机海报数
     * @param Request $request
     * @param $count
     * @return Poster[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function random( Request $request, $count )
    {
        if($type = $request->cate_type) {
            $posters = Poster::query()->where(['poster_type' => $type, 'poster_id' => $request->cate_id])->inRandomOrder()->limit($count)->get();
        } else {
            $posters = Poster::query()->inRandomOrder()->limit($count)->get();
        }

        return $posters;
    }

    /**
     * 当前分类下上一个或下一下海报
     * @param PosterService $service
     * @param Request $request
     * @return PosterService|\Illuminate\Database\Eloquent\Model|null|object
     */
    public function nextOrLast( PosterService $service, Request $request )
    {
        $poster = $service->nextOrLast($request->id, $request->cate, $request->cate_id, $request->type);

        return $this->response->array([
            'code' => 200,
            'data' => $poster
        ]);
    }

    /**
     * 推送海报到微信公众号
     * @param Request $request
     * @return mixed
     */
    public function sendPoster( Request $request )
    {
        $user = $this->user();
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $request->image, $result)) {
            $file_name = "user/inviting/" . str_random(32) . '.' . $result[ 2 ];
            \Storage::put($file_name, base64_decode(str_replace($result[ 1 ], '', $request->image)));
            dispatch(new InvitationPoster($user->openid, $file_name));
        } else {
            return $this->response->array([
                'code' => 202,
                'message' => '不是base64图片'
            ]);
        }

        return $this->response->array([
            'code' => 200,
            'message' => '已发送海报'
        ]);
    }
}
