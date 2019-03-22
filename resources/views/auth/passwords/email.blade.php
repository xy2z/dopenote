@extends('assets.layout')

@section('title', __('Forgot Password'))

@section('styles')
	<link rel="stylesheet" href="/css/layout.css" />
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
						@if (session('status'))
						<div class="notification is-success">
							{{ session('status') }}
						</div>
						@endif

						<form method="POST" action="{{ route('password.email') }}">
							@csrf

							<h1 class="subtitle">{{ __('Forgot Password') }}</h1>

							<p class="has-text-grey">
								Enter the email address associated with your Dopenote account, then click Continue.
							</p>
							<br />

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
												value="{{ old('email') }}"
												class="input {{ $errors->has('email') ? ' is-danger' : '' }}
											">
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

							{{-- Submit --}}
							<div class="field is-horizontal">
								<div class="field-label"></div>
								<div class="field-body">
									<div class="field">
										<div class="control">
											<button class="button is-info">
												Continue
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
						<a href="/register">Register</a>
					</div>
				</div>

			</div>
		</div>
	</section>
@endsection
