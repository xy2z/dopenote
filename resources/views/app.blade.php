@extends('assets.layout')

@php
	$bulma_disable = true
@endphp

@section('styles')
	<link rel="stylesheet" href="/css/layout.css">
	<link rel="stylesheet" href="/css/nav.css">
	<link rel="stylesheet" href="/css/editor.css">
	<link rel="stylesheet" href="/css/buttons.css">

	{{-- User settings styles --}}
	<style>
		.tox-edit-area {
			font-family: '{{ $user_settings->font_family }}' !important;
		}
	</style>
@endsection

@section('scripts')
	<script src="/js/app.js"></script>
@endsection

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
			<div class="logo-wrapper">
				<a class="logo ondark is-small" href="/">Dopenote</a>
			</div>

			<button class="action center" v-on:click="create_notebook()" :disabled="waiting_for_ajax">New Notebook</button>

			<hr class="center">

			{{-- Views: Starred, trash --}}
			<div class="nav-notebooks">
				<a
					v-for="view in views"
					v-on:click="set_view(view)"
					v-bind:class="get_view_class(view)"
					>@{{ view.title }}
				</a>
			</div>

			<br>

			{{-- List Notebooks --}}
			<div class="nav-notebooks-header">Notebooks</div>
			<draggable class="nav-notebooks" draggable=".notebook_link" @end="notebooks_draggable_change" ref="notebooks">
				<a
					class="notebook_link"
					v-for="notebook in notebooks"
					v-on:click="view_notebook(notebook)"
					v-bind:class="get_notebook_class(notebook)"
					@contextmenu.prevent.stop="notebook_context_menu_show($event, notebook)"
					:data-notebookid="notebook.id" {{-- Used by Draggable --}}
					:key="notebook.id"
					ref="notebook_item"
					>
					@{{ notebook.title }}
				</a>
			</draggable>

			<br>
			<hr class="center">
			<br>

			<div class="nav-bottom-links">
				<span>Signed in as <strong>{{ Auth::user()->name }}</strong></span>
				{{-- <a href="{{ Config::get('app.github_url') }}">Dopenote v{{ Config::get('app.version') }}</a> --}}
				<a href="{{ route('user_settings') }}">User Settings</a>
				<a href="{{ route('user_logout') }}">Log Out</a>
			</div>

		</nav>


		{{-- List Notes --}}
		<div class="notes-list">
			<button
				class="action center"
				v-on:click="create_note()"
				v-if="active_notebook_id"
				:disabled="waiting_for_ajax">New Note</button>

			<div v-if="get_note_list().length">
				<a
					v-for="note in get_note_list()"
					:href="'#/note/' + note.id"
					v-on:click="view_note(note)"
					v-bind:class="get_note_class(note)"
					>
					@{{ get_note_title(note) }}
				</a>
			</div>
			<div class="empty-note-list" v-else>
				@{{ get_empty_note_list_text() }}
			</div>

		</div>


		{{-- Note Content Editor --}}
		<div id="note" v-if="notes.length">
			<div id="actions" v-if="notes.length">
				{{-- Buttons for active notes --}}
				<span v-if="getActiveNote() && getActiveNote().deleted_at === null">
					<button
						v-on:click="toggle_star(getActiveNote())"
						:disabled="waiting_for_ajax"
						class="note-action fas fa-star"
						v-bind:class="getStarClass(getActiveNote())"
						v-tooltip="'Star note'"
						></button>

					<button
						v-on:click="delete_note(getActiveNote())"
						:disabled="waiting_for_ajax"
						class="note-action fas fa-trash"
						v-tooltip="'Trash note'"
						></button>
				</span>

				{{-- Buttons for deleted notes --}}
				<span v-if="getActiveNote() && getActiveNote().deleted_at">
					<button
						v-on:click="restore_note(getActiveNote())"
						:disabled="waiting_for_ajax"
						class="note-action fas fa-trash-restore"
						v-tooltip="'Restore note'"
						></button>

					<button
						v-on:click="perm_delete_note(getActiveNote())"
						:disabled="waiting_for_ajax"
						title="Permanently delete note"
						class="note-action fas fa-trash-alt red"
						></button>
				</span>
			</div>

			<div class="note-title">
				<input
					type="text"
					ref="note_title"
					v-if="getActiveNote()"
					v-model="getActiveNote().title"
					:disabled="getActiveNote().deleted_at"
					@change="set_title(getActiveNote())"
					placeholder="New note"
				>
			</div>

			<div class="note-content" v-if="getActiveNote()">
				<editor-content ref="editor" id="editor"></editor-content>
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
