@extends('assets.layout', [
	// 'background' => '/img/login-background-chill.jpg',
])

@section('title', 'Login')

@section('styles')
<link rel="stylesheet" href="/css/app.min.css">
@endsection

@section('content')
<section class="hero is-fullheight" style="background: url('/img/login-background-chill.jpg'); background-size: cover; background-position: center 0;">
	<div class="hero-body" style="background: rgba(255, 255, 255, 0.4);">
		<div class="container has-text-centered">

			<header class="title logo">
				Dopenote
			</header>

			<div class="column is-4 is-offset-4">
				<div class="box">
					<form method="POST" action="{{ route('login') }}">
						@csrf

						@include('assets.errors_any')

						<h1 class="subtitle">Login</h1>

						{{-- Email --}}
						<div class="field">
							<div class="control has-icons-left has-icons-right">
								<input
								type="email"
								name="email"
								placeholder="Email"
								required
								autofocus
								value="{{ old('email') }}"
								class="input {{ $errors->has('email') ? ' is-danger' : '' }}"
								>
								<span class="icon is-small is-left">
									<i class="fas fa-envelope"></i>
								</span>
							</div>
						</div>

						{{-- Password --}}
						<div class="field">
							<div class="control has-icons-left">
								<input
								type="password"
								name="password"
								placeholder="Password"
								required
								class="input {{ $errors->has('password') ? ' is-danger' : '' }}"
								>
								<span class="icon is-small is-left">
									<i class="fas fa-lock"></i>
								</span>
							</div>
						</div>

						{{-- Remember me --}}
						<label class="checkbox">
							<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
							{{ __('Remember me') }}
						</label>

						<br>
						<br>

						{{-- Submit --}}
						<div class="field">
							<p class="control">
								<button class="button is-fullwidth is-info">
									Login
								</button>
							</p>
						</div>
					</form>

					<div style="margin-top: 10px">
						@if (!empty(config()->get('services.facebook.client_id')))
						<div  class="field">
							<p class="control">
								<a class="button is-fullwidth is-info" href="{{url('/login/redirect/facebook')}}"><i class="fab fa-facebook-f"></i> &nbsp; Login With Facebook
								</a>
							</p>
						</div>
						@endif
						@if (!empty(config()->get('services.google.client_id')))
						<div class="field">
							<p class="control">
								<a class="button is-fullwidth is-info" href="{{url('/login/redirect/google')}}"><i class="fab fa-google"></i> &nbsp; Login With Google
								</a>
							</p>
						</div>
						@endif
						@if (!empty(config()->get('services.twitter.client_id')))
						<div class="field">
							<p class="control">
								<a class="button is-fullwidth is-info" href="{{url('/login/redirect/twitter')}}"><i class="fab fa-twitter"></i> &nbsp;	Login With Twitter
								</a>

							</p>
						</div>
						@endif
					</div>


					{{-- Links --}}
					<div class="content has-text-grey is-small">
						<a href="/register">Register</a> &nbsp;·&nbsp;

						@if (Route::has('password.request'))
						<a class="btn btn-link" href="{{ route('password.request') }}">
							{{ __('Forgot Password') }}
						</a>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>
	@endsection
