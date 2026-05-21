<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AdminLogin extends Component
{
    public $username = '';
    public $password = '';

    public function login()
    {
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $this->username, 'password' => $this->password])) {
            session()->regenerate();
            return redirect()->to('/admin');
        }

        session()->flash('login_error', 'Credenciales de supervisor incorrectas.');
    }

    public function render()
    {
        return view('livewire.admin-login')->title('Acceso Supervisor - Arancalo');
    }
}
