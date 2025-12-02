<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedMail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailTestController extends Controller
{
    /**
     * Afficher la page de test des emails.
     */
    public function index(): View
    {
        return view('mail-test.index');
    }

    /**
     * Envoyer un email de test pour la création d'utilisateur avec mot de passe temporaire.
     */
    public function sendUserCreated(Request $request): RedirectResponse
    {
        $user = $request->user();
        $temporaryPassword = 'PasswordTest123!';

        try {
            Mail::to($user->email)->send(new UserCreatedMail($user, $temporaryPassword));

            return redirect()
                ->route('mail-test.index')
                ->with('success', 'Email "Création d\'utilisateur (avec mot de passe temporaire)" envoyé avec succès à ' . $user->email . ' !');
        } catch (\Exception $e) {
            return redirect()
                ->route('mail-test.index')
                ->with('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }

    /**
     * Envoyer un email de test pour la création d'utilisateur (propriétaire - sans mot de passe temporaire).
     */
    public function sendUserCreatedOwner(Request $request): RedirectResponse
    {
        $user = $request->user();

        try {
            Mail::to($user->email)->send(new UserCreatedMail($user));

            return redirect()
                ->route('mail-test.index')
                ->with('success', 'Email "Création d\'utilisateur (propriétaire)" envoyé avec succès à ' . $user->email . ' !');
        } catch (\Exception $e) {
            return redirect()
                ->route('mail-test.index')
                ->with('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }
}
