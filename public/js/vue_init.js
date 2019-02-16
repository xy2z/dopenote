var vueApp = new Vue({
	el: "#app",
	data: app_data,

	mounted: function() {
		this.sort_notes()
		this.load_note_from_hash();

		// On hash change
		window.onhashchange = this.onhashchange

	},
	methods: {
		/**
		 * Get current status
		 *
		 */
		get_status: function() {
			return this.waiting_for_ajax ? 'Saving...' : 'Saved.'
		},

		/**
		 * Sort notes
		 *
		 */
		sort_notes: function() {
			// Sort by last updated_at
			this.notes.sort(function(a,b) {
				return a.updated_at < b.updated_at ? 1 : -1

			})

			// Sort by starred first.
			this.notes.sort(function(a,b) {
				return a.starred ? -1 : 1
			})
		},

		/**
		 * Get the active (currently viewed) note
		 *
		 */
		getActiveNote: function() {
			return this.getNoteByID(this.active_note_id)
		},

		/**
		 * Get note by ID
		 *
		 */
		getNoteByID: function(note_id) {
			let find_note

			this.notes.some(note => {
				if (note && note.id === note_id) {
					find_note = note
					return true
				}
			})

			return find_note

		},

		/**
		 * Get note link classes
		 *
		 */
		get_note_class: function(note) {
			return {
				'active': note.id === this.active_note_id,
				'starred': note.starred
			}
		},

		get_notebook_class: function(notebook) {
			return {
				'active': notebook.id === this.active_notebook_id,
				'no_selection': true,
			}
		},

		/**
		 * View a note.
		 * Use this instead of setting active_note_id.
		 *
		 */
		view_note: function(note) {
			this.active_note_id = note.id
			this.active_notebook_id = note.notebook_id
			document.title = this.get_title(note) + ' | Dopenote'
			window.location.hash = '#/note/' + note.id

			if (typeof tinymce !== 'undefined') {
				// "tinymce" variable is unset first time page loads.
				tinymce.get('editor').setContent(note.content)
			}
		},

		/**
		 * Delete a note.
		 *
		 */
		delete_note: function(delete_note) {
			// Confirm: Disabled while developing.
			if (!confirm('Are you sure you want to delete this note?')) {
				return
			}

			this.waiting_for_ajax = true
			axios
			.post('/note/' + delete_note.id + '/delete')
			.then(response => {
				this.waiting_for_ajax = false

				// Set active note id for first note not deleted.
				for (var note_id in this.notes) {
					let note = this.notes[note_id]
					if (note.id !== delete_note.id) {
						// this.active_note_id = note.id
						this.view_note(note)
						break
					}
				}

				// Find the note index to delete.
				let delete_note_index

				this.notes.some(function(note, index) {
					if (note.id === delete_note.id) {
						delete_note_index = index
						return true
					}
				})

				this.notes.splice(delete_note_index, 1)
			})
		},

		/**
		 * Create a new note.
		 *
		 */
		create_note: function() {
			this.waiting_for_ajax = true

			axios
			.post('/note/create', {
				notebook_id: this.active_notebook_id
			})
			.then(response => {
				this.waiting_for_ajax = false

				let note = response.data.note

				this.notes.push(note)
				this.sort_notes()
				this.view_note(note)

				this.$refs.note_title.focus()
			})
		},

		/**
		 * Create a new notebook.
		 *
		 */
		create_notebook: function() {
			this.waiting_for_ajax = true

			axios
			.post('/notebook/create',)
			.then(response => {
				this.waiting_for_ajax = false

				let notebook = response.data.notebook

				this.notebooks.push(notebook)
				// this.sort_notes()
				// this.view_note(note)

				// this.$refs.note_title.focus()
			})
		},

		view_notebook: function(notebook) {
			this.active_notebook_id = notebook.id

		},

		/**
		 * Toggle star on note.
		 *
		 */
		toggle_star: function(note) {
			this.waiting_for_ajax = true

			axios
			.post('/note/' + note.id + '/toggle_star')
			.then(response => {
				this.waiting_for_ajax = false

				note.starred = response.data.note.starred
				note.updated_at = response.data.note.updated_at
				this.sort_notes()
			})
		},

		/**
		 * Update note (changes note.updated_at)
		 *
		 */
		set_content: function(note, content) {
			this.waiting_for_ajax = true

			axios.post('/note/' + note.id + '/set_content', {
				content: content
			})
			.then(response => {
				this.waiting_for_ajax = false

				note.content = content
				note.updated_at = response.data.note.updated_at
				this.sort_notes()
			})
		},

		/**
		 * Title change.
		 *
		 */
		set_title: function(note) {
			this.waiting_for_ajax = true

			axios.post('/note/' + note.id + '/set_title', {
				title: note.title
			})
			.then(response => {
				this.waiting_for_ajax = false

				note.updated_at = response.data.note.updated_at
				this.sort_notes()
			})
		},

		/**
		 * Get note title
		 * Made into a made in case it's empty (when it's new)
		 *
		 */
		get_title: function(note) {
			if (note.title.length) {
				return note.title
			}

			return '(untitled)'
		},

		/**
		 * Get class for the 'star' button
		 *
		 */
		getStarClass: function(note) {
			return {
				'star': true,
				'starred': note.starred
			}
		},

		edit_notebook: function(notebook) {
			console.log('Todo: edit notebook: ' + notebook.id)
		},

		load_note_from_hash: function() {
			// Get note ID from URL hash, eg. '#/note/123'
			let hash = window.location.hash
			let split = hash.split('/')
			if (split[1] == 'note') {
				// Render note.
				let note_id = parseInt(split[2])
				if (note = this.getNoteByID(note_id)) {
					// this.active_note_id = note_id
					this.view_note(note)
				} else {
					alert('Note not found.')
				}
			}
		},

		onhashchange: function() {
			this.load_note_from_hash();
		},
	}
})