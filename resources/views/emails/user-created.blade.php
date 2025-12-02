<x-mail::message>
# Bienvenue {{ $user->first_name }} !

Votre compte a été créé sur {{ config('app.name') }}.

**Vos identifiants de connexion :**

- **Email :** {{ $user->email }}
@if($temporaryPassword)
- **Mot de passe temporaire :** `{{ $temporaryPassword }}`

<x-mail::panel>
⚠️ **Important :** Vous devrez changer votre mot de passe lors de votre première connexion.
</x-mail::panel>
@else
<x-mail::panel>
✅ Votre compte est prêt ! Vous pouvez vous connecter avec le mot de passe que vous avez défini.
</x-mail::panel>
@endif

<x-mail::button :url="route('login')">
Se connecter
</x-mail::button>

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
