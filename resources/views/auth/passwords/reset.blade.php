@extends('assets.layout')

@section('title', __('Reset Password'))

@section('styles')
	<link rel="stylesheet" href="/css/app.min.css">
@endsection

@section('content')
	 <section class="hero is-fullheight" style="background: #eee;">
		<div class="hero-body">
			<div class="container has-text-centered">
				<header class="title logo">
					Dopenote
				</header>

				<div class="column">
					<div class="box">
						<form method="POST" action="{{ route('password.update') }}">
							<input type="hidden" name="token" value="{{ $token }}">
							@csrf

							<h1 class="subtitle">{{ __('Reset Password') }}</h1>

							{{-- Email --}}
							<div class="field is-horizontal">
								<div class="field-label is-normal">
									<label class="label">Email</label>
								</div>
								<div class="field-body">
									<div class="field">
										<div class="control has-icons-left has-icons-right">
											<input
												type="text"
												name="email"
												placeholder="Email"
												required
												autofocus
												value="{{ $email ?? old('email') }}"
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
								</div>
							</div>

							{{-- Password --}}
							<div class="field is-horizontal">
								<div class="field-label is-normal">
									<label class="label">Password</label>
								</div>
								<div class="field-body">
									<div class="field">
										<div class="control has-icons-left has-icons-right">
											<input
												type="password"
												name="password"
												placeholder="Password"
												required
												value="{{ old('password') }}"
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
								</div>
							</div>

							{{-- Password Confirmation --}}
							<div class="field is-horizontal">
								<div class="field-label is-normal">
									<label class="label">Re-enter Password</label>
								</div>
								<div class="field-body">
									<div class="field">
										<div class="control has-icons-left has-icons-right">
											<input
												type="password"
												name="password_confirmation"
												placeholder="Re-enter Password"
												required
												value="{{ old('password_confirmation') }}"
												class="input {{ $errors->has('password_confirmation') ? ' is-danger' : '' }}"
											>
											<span class="icon is-small is-left">
												<i class="fas fa-lock"></i>
											</span>
										</div>
										@if ($errors->has('password_confirmation'))
											<p class="help is-danger" role="alert">
												{{ $errors->first('password_confirmation') }}
											</p>
										@endif
									</div>
								</div>
							</div>

							{{-- Submit --}}
							<div class="field is-horizontal">
								<div class="field-label"></div>
								<div class="field-body">
									<div class="field">
										<div class="control">
											<button class="button is-info">
												{{ __('Reset Password') }}
											</button>
										</div>
									</div>
								</div>
							</div>

						</form>
					</div>


					<!-- Links -->
					<div class="content has-text-grey is-small">
						<a href="/login">Login</a> &nbsp;·&nbsp;

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
