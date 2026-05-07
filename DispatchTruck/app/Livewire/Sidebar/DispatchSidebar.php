<?php

namespace App\Livewire\Sidebar;

use Livewire\Component;
use App\Livewire\Actions\Logout;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class DispatchSidebar extends Component
{
    protected $listeners = ['logoutConfirmed' => 'logout'];

    public function confirmLogout()
    {
        $this->dispatch('show-logout-confirmation');
    }

    public function logout(Logout $logout)
    {
        return $logout();
    }

    public function render()
    {
        return view('livewire.sidebar.dispatch-sidebar');
    }
}