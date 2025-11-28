<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * L'inscription publique est désactivée - seuls les admins peuvent créer des comptes.
     */
    public function create(): View
    {
        // L'inscription publique est désactivée
        abort(404, 'L\'inscription publique n\'est pas disponible. Contactez un administrateur pour créer un compte.');
    }

    /**
     * Handle an incoming registration request.
     * L'inscription publique est désactivée - seuls les admins peuvent créer des comptes.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // L'inscription publique est désactivée
        abort(404, 'L\'inscription publique n\'est pas disponible. Contactez un administrateur pour créer un compte.');
    }
}
