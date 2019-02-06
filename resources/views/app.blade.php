@extends('assets.layout')

@section('title', $note->title)

@section('content')
	<div id="app">

		<div id="status">
			Status here.
		</div>

		<nav>
			<a class="logo" href="/">dopenote</a>

			<form method="post" action="/note/create">
				@csrf
				<button type="submit">New Note</button>
			</form>

			<div class="nav-notes">
				@foreach ($notes as $row)
					<a href="/note/{{ $row->id }}">{{ $row->title }}</a>
				@endforeach
			</div>
		</nav>

		<div id="note">
			<div class="note-title">
				{{ $note->title }}
			</div>
			<div class="note-content">
				{!! $note->content !!}
			</div>
		</div>

	</div>
@endsection
