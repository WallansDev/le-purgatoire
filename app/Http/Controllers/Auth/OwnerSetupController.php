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

class OwnerSetupController extends Controller
{
    /**
     * Afficher le formulaire de création du compte owner initial.
     */
    public function create(): View
    {
        // Vérifier qu'aucun utilisateur n'existe déjà
        if (User::count() > 0) {
            abort(404);
        }

        return view('auth.setup-owner');
    }

    /**
     * Traiter la création du compte owner initial.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Vérifier qu'aucun utilisateur n'existe déjà
        if (User::count() > 0) {
            abort(404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        [$firstName, $lastName] = array_pad(explode(' ', $request->name, 2), 2, null);

        // Créer le premier utilisateur avec les droits d'owner (is_admin = true)
        $user = User::create([
            'first_name' => $firstName ?? $request->name,
            'last_name' => $lastName,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
            'must_change_password' => false,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

