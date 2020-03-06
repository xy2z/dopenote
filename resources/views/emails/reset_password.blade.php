@extends('assets.mail_layout')

@section('content')
	Hello {{ $user->name }},<br>
	<br>
	You recently requested a password reset.<br>
	<br>
	To reset your password use this link: <a href="{{ request()->root() }}/password/reset/{{ $token }}">{{ request()->root() }}/password/reset/{{ $token }}</a><br>
	<br>
	<br>
	If you didn't request a new password, just ignore this email.
@endsection
