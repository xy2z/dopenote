@extends('assets.layout')

@section('content')
	<div id="app">
		<div id="status">
			@{{ get_status() }}
		</div>

		<nav>
			<a class="logo" href="/">dopenote</a>

			<button v-on:click="create_note()" :disabled="waiting_for_ajax">Create note</button>

			<div class="nav-notes">
				<a
					:href="'#/note/' + note.id"
					v-on:click="view_note(note)"
					v-for="note in notes"
					v-bind:class="navGetClass(note)"
					>
					@{{ get_title(note) }}
				</a>
			</div>

		</nav>


		<div id="note" v-if="notes.length">
			<div id="actions" v-if="notes.length">
				<button v-on:click="toggle_star(getActiveNote())" :disabled="waiting_for_ajax" v-bind:class="getStarClass(getActiveNote())">&star;</button>
				<button v-on:click="delete_note(getActiveNote())" :disabled="waiting_for_ajax">Delete Note</button>
			</div>
			<br />

			<div class="note-title">
				<input type="text" ref="note_title" v-model="getActiveNote().title" @change="set_title(getActiveNote())" placeholder="New note" />
			</div>

			<div class="note-content">
				<textarea id="editor">@{{ getActiveNote().content }}</textarea>
			</div>
		</div>

	</div>

	<script>
		var app_data = @json($app_data)
	</script>
@endsection
