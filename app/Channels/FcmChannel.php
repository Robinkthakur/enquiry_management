<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\FirebaseService;
use App\Models\FcmToken;

class FcmChannel
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        if (!$message || empty($message['title'])) {
            return;
        }

        $title = $message['title'];
        $body = $message['body'] ?? '';
        $data = $message['data'] ?? [];

        // The $notifiable could be a User, or an Admission which has a user.
        // Let's check both for robust routing!
        $user = null;
        if ($notifiable instanceof \App\Models\User) {
            $user = $notifiable;
        } elseif (method_exists($notifiable, 'user')) {
            $user = $notifiable->user;
        } elseif (isset($notifiable->user)) {
            $user = $notifiable->user;
        }

        if (!$user) {
            return;
        }

        $this->firebaseService->sendToUser($user, $title, $body, $data);
    }
}
