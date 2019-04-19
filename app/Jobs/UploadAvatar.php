<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class UploadAvatar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;

    protected $avatar;

    /**
     * Create a new job instance.
     *
     * @param $user_id
     * @param $avatar
     */
    public function __construct($user_id, $avatar)
    {
        $this->user_id = $user_id;
        $this->avatar = $avatar;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->avatar) {
            $file_name = "user/avatar/" . date('Ymd'). "/" . str_random(24) . '.jpg';
            \Storage::disk('admin')->put($file_name, uploadImageBase64($this->avatar));
            User::query()->where('id', $this->user_id)->update([ 'avatar' => '/' . $file_name ]);
        } else {
            User::query()->where('id', $this->user_id)->update([ 'avatar' => '/user/user_avatar.jpg' ]);
        }
    }
}
