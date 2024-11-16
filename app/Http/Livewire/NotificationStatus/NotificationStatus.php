<?php

namespace App\Http\Livewire\NotificationStatus;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationStatus extends Component
{
    public function render()
    {
        $notifications = Notification::where('user_id', Auth::id())->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        // dd($notifications);
        return view('livewire.notification-status.notification-status', compact('notifications', 'unreadCount'));
    }
}
