@if($errors->any())
	<div class="notification is-danger">
		<strong>Error</strong><br>
		@foreach ($errors->all() as $msg)
			<div> &bull; {{ $msg }}</div>
		@endforeach
	</div>
@endif