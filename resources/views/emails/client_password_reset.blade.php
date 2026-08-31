@extends('emails.layouts.transactional', [
    'emailTitle' => 'Réinitialisez votre mot de passe',
    'preheader' => 'Un nouveau lien sécurisé a été demandé pour votre espace client SOLUTCLOUD.',
    'emailCategory' => 'Sécurité du compte',
    'emailBadge' => 'Réinitialisation',
])

@section('content')
    <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
    <p>Une demande de réinitialisation du mot de passe de votre espace client SOLUTCLOUD a été effectuée.</p>
    <p>Si vous n’êtes pas à l’origine de cette demande, ignorez simplement cet e-mail : votre mot de passe actuel reste inchangé.</p>
@endsection

@section('action')
    <a href="{{ $resetUrl }}" class="email-button">Choisir un nouveau mot de passe</a>
@endsection

@section('notice')
    Ce lien est personnel et temporaire. Pour votre sécurité, SOLUTCLOUD ne connaît pas et ne communique jamais votre mot de passe.
@endsection