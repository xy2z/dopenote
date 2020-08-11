@extends('assets.layout')

@section('styles')
		<link rel="stylesheet" href="/css/app.min.css">
@endsection

@section('content')
	<section class="section">
		<div style="width: 900px; margin: 0 auto; max-width: 100%;">

			<a href="/" class="title logo has-text-centered">
				Dopenote
			</a>

			<form method="post" class="form-check-unsaved">
				@csrf

				<div class="header">
					<h1 class="title">User Settings</h1>

					<a href="/">
						<i class="fas fa-chevron-left"></i> Back to app
					</a>
				</div>

				@include('assets.success')
				@include('assets.errors_any')

				<h2 class="subtitle">User</h2>
				<fieldset class="content">
					@include('assets.form_field', [
						'label' => 'Username',
						'name' => 'name',
						'value' => Auth::user()->name,
						'disabled' => true
					])

					@include('assets.form_field', [
						'label' => 'E-mail',
						'name' => 'email',
						'type' => 'email',
						'value' => Auth::user()->email,
					])
				</fieldset>

				<hr class="bold">

				<h2 class="subtitle">Change Password</h2>
				<fieldset class="content">
					@include('assets.form_field', [
						'label' => 'Current password',
						'type' => 'password',
						'name' => 'current_password',
					])

					@include('assets.form_field', [
						'label' => 'New password',
						'type' => 'password',
						'name' => 'new_password',
					])

					@include('assets.form_field', [
						'label' => 'Confirm new password',
						'type' => 'password',
						'name' => 'new_password_confirmation',
					])
				</fieldset>

				<hr class="bold">

				<h2 class="subtitle">Export notes</h2>
				<div class="field is-horizontal">
						<p>Save all your notes in a zip file.</p>
				</div>
				<fieldset class="content">
					@include('assets.form_link', [
						'label' => 'Export notes',
						'value' => 'Export',
						'url' => route('user_settings_export')
					])
				</fieldset>

				<hr class="bold">

				<h2 class="subtitle">Editor Settings</h2>
				<fieldset class="content">
					@include('assets.form_field', [
						'label' => 'Font size',
						'type' => 'number',
						'name' => 'font_size',
						'value' => $font_size,
						'static' => 'px',
						'min' => 4,
						'max' => 30,
					])

					@include('assets.form_field', [
						'label' => 'Font family',
						'element' => 'select',
						'name' => 'font_family',
						'value' => $font_family,
						'options' => Config::get('app.fonts'),
						'set_select_option_as_font' => true,
						'select_no_key' => true,
					])

					@include('assets.form_field', [
						'label' => 'Line height',
						'type' => 'number',
						'name' => 'line_height',
						'value' => $line_height,
						'static' => 'em',
						'step' => '0.1',
						'min' => '1.0',
						'max' => '5.0',
					])

					@include('assets.form_field', [
						'label' => 'Paragraph margin',
						'type' => 'number',
						'name' => 'paragraph_margin',
						'value' => $paragraph_margin,
						'static' => 'em',
						'step' => '0.1',
						'min' => '1.0',
						'max' => '5.0',
					])

				</fieldset>

				{{-- Submit --}}
				@include('assets.form_submit', [
					'text' => 'Save'
				])

			</form>

		</div>
	</section>
@endsection
