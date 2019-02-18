<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/8 0008
 * Time: 下午 5:10
 */

namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function MongoDB\BSON\toJSON;

class UploadController extends Controller
{
    public function upload( Request $request )
    {
        $type = $request->type;
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $request->image, $result)) {
            $file_name = "user/{$type}/". date('Ymd') . '/' . str_random(32) . '.' . $result[ 2 ];
            \Storage::put($file_name, base64_decode(str_replace($result[ 1 ], '', $request->image)));
        }  else {
            return $this->response->array([
                'code' => 202,
                'message' => '不是base64图片'
            ]);
        }
//                $file_name = "user/{$type}/". date('Ym') . '/' . str_random(32) . '.' . $request->image->getClientOriginalExtension();
//                \Storage::put($file_name, $request->image);

        return $this->response->array([
            'code' => 0,
            'message' => '上传成功',
            'path' => \Storage::url($file_name)
        ]);
    }
}
