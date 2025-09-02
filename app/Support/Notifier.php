<?php
// app/Support/Notifier.php

namespace App\Support;

use App\Models\User; 

use Illuminate\Support\Facades\DB;

class Notifier {
  public static function send(User $user, string $type, string $title, string $body, array $data=[], ?string $link=null): void {
    $user->notifications()->create(['type'=>$type,'title'=>$title,'message'=>$body,'link'=>$link]);
    if (!($user->push_notifications_enabled ?? true)) return;
    $tokens = method_exists($user,'deviceTokens')
      ? $user->deviceTokens()->where('is_enabled',1)->pluck('token')->all()
      : DB::table('device_tokens')->where('user_id',$user->id)->where('is_enabled',1)->pluck('token')->all();
    if (!$tokens) return;
    app(\App\Services\FcmHttpV1Service::class)->sendToTokens($tokens, ['title'=>$title,'body'=>$body], $link ? array_merge($data,['link'=>$link]) : $data);
  }
}