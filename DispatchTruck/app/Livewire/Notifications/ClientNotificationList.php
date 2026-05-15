<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ClientNotificationList extends Component
{
    public $notifications;
    public $unreadCount;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();
        $this->notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $this->unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->find($notificationId);

        if ($notification) {
            $notification->update(['is_read' => true]);
            $this->loadNotifications();
            session()->flash('message', 'Notification marked as read.');
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->loadNotifications();
        session()->flash('message', 'All notifications marked as read.');
    }

    public function render()
    {
        return view('livewire.notifications.client-notification-list');
    }
}