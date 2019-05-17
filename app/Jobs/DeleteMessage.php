<?php

namespace App\Jobs;

use App\Models\MessageFamily;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message_id;

    /**
     * Create a new job instance.
     *
     * @param $message_id
     */
    public function __construct($message_id)
    {
        $this->message_id = $message_id;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $message = MessageFamily::with('user:id,member_lock_at')->where('id', $this->message_id)->first();
        if(!$message->user->member_lock_at || Carbon::parse($message->user->member_lock_at)->lt(now())) {
            $message->delete();
        }
    }
}
