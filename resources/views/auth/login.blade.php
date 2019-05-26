@extends('assets.layout')

@section('title', 'Login')

@section('styles')
	<link rel="stylesheet" href="/css/layout.css">
@endsection

@section('content')
	<section class="hero is-fullheight" style="background: #eee;">
		<div class="hero-body">
			<div class="container has-text-centered">
				<header class="title logo">
					Dopenote
				</header>

				<div class="column is-4 is-offset-4">
					<div class="box">
						<form method="POST" action="{{ route('login') }}">
							@csrf

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
								@if ($errors->has('email'))
									<p class="help is-danger" role="alert">
										{{ $errors->first('email') }}
									</p>
								@endif
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
								@if ($errors->has('password'))
									<p class="help is-danger" role="alert">
										{{ $errors->first('password') }}
									</p>
								@endif
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
