@extends('assets.layout')

@section('content')
	<div class="browser_not_supported" style="display: none;">
		This browser is not supported. Please use a modern browser.
	</div>


	<div id="app">

		{{-- Status (for saving changes) --}}
		<div id="status">
			@{{ get_status() }}
		</div>


		{{-- Notebooks Sidebar --}}
		<nav>
			<a class="logo" href="/">dopenote</a>

			<button class="action" v-on:click="create_notebook()" :disabled="waiting_for_ajax">New Notebook</button>

			<div class="nav-notebooks">
				<a
					v-for="view in views"
					v-on:click="set_view(view)"
					v-bind:class="get_view_class(view)"
					>@{{ view.title }}
				</a>
			</div>

			<br />

			<div class="nav-notebooks-header">Notebooks</div>
			<div class="nav-notebooks">
				<a
					v-for="notebook in notebooks"
					v-on:click="view_notebook(notebook)"
					v-on:dblclick="edit_notebook(notebook)"
					v-bind:class="get_notebook_class(notebook)"
					@contextmenu.prevent.stop="notebook_context_menu_show($event, notebook)"
					>
					@{{ notebook.title }}
				</a>
			</div>

			<br />
			<br />
			<hr />
			<br />
			<br />

			<div class="nav-bottom-links">
				{{-- <a href="{{ Config::get('app.github_url') }}">Dopenote v{{ Config::get('app.version') }}</a> --}}
				<a href="{{ route('user_settings') }}">User Settings</a>
				<a href="{{ route('user_logout') }}">Log Out</a>
			</div>

		</nav>


		{{-- List Notes --}}
		<div class="notes-list">
			<button
				class="action"
				v-on:click="create_note()"
				v-if="active_notebook_id"
				:disabled="waiting_for_ajax">New Note</button>

			<a
				v-for="note in notes"
				{{-- v-if="note.notebook_id === active_notebook_id" --}}
				v-if="render_note_in_list(note)"
				:href="'#/note/' + note.id"
				v-on:click="view_note(note)"
				v-bind:class="get_note_class(note)"
				>
				@{{ get_note_title(note) }}
			</a>

		</div>


		{{-- Note Content Editor --}}
		<div id="note" v-if="notes.length">
			<div id="actions" v-if="notes.length">
				<button v-on:click="toggle_star(getActiveNote())" :disabled="waiting_for_ajax" v-bind:class="getStarClass(getActiveNote())">&star;</button>
				<button v-on:click="delete_note(getActiveNote())" :disabled="waiting_for_ajax">Delete Note</button>
			</div>
			<br />

			<div class="note-title">
				<input type="text" ref="note_title" v-if="getActiveNote()" v-model="getActiveNote().title" @change="set_title(getActiveNote())" placeholder="New note" />
			</div>

			<div class="note-content" v-if="getActiveNote()">
				<textarea id="editor">@{{ getActiveNote().content }}</textarea>
			</div>
		</div>


		{{-- Component: Notebook context menu --}}
		<vue-simple-context-menu
			:element-id="'myFirstMenu'"
			:options="notebook_context_menu"
			:ref="'vueSimpleContextMenu1'"
			@option-clicked="notebook_context_menu_action">
		</vue-simple-context-menu>

	</div>

	<script>
		var app_data = @json($app_data)
	</script>
@endsection
