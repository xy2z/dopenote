@if (Session::has('success'))
	<div class="notification is-success">
		<i class="fas fa-check"></i>
		@if (Session::get('success') === true)
			{{-- No success message --}}
			Saved.
		@else
			{{ Session::get('success') }}
		@endif
	</div>
@endif