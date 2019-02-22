// Views (starred, trash)
app_data.active_view_label = null;
app_data.views = [
    // Starred
    {
        title: 'Starred',
        label: 'starred',
    },

    // Trash (todo)
    {
        title: 'Trash (todo)',
        label: 'trash',
    },
]


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
            return this.notes.find(note => note.id === note_id)
        },

        /**
         * Get notebook by ID
         *
         */
        getNotebookByID: function(notebook_id) {
            return this.notebooks.find(notebook => notebook.id === notebook_id)
        },

        /**
         * Get view by label
         *
         */
        getViewByLabel: function(label) {
            return this.views.find(view => view && view.label === label)
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

        /**
         * Get class for notebook list element
         *
         */
        get_notebook_class: function(notebook) {
            return {
                'active': notebook.id === this.active_notebook_id,
                'no_selection': true,
            }
        },

        /**
         * Get class for view (starred, trash)
         *
         */
        get_view_class: function(view) {
            return {
                'active': !this.active_notebook_id && view.label === this.active_view_label,
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

            if (!this.active_view_label) {
                this.active_notebook_id = note.notebook_id
            }

            document.title = this.get_note_title(note) + ' | Dopenote'
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

            let found = false
            this.waiting_for_ajax = true

            axios
            .post('/note/' + delete_note.id + '/delete')
            .then(response => {
                this.waiting_for_ajax = false

                // Set the active note to the first note in current notebook.
                for (var note_id in this.notes) {
                    let note = this.notes[note_id]

                    if (note.id === delete_note.id) {
                        continue
                    }

                    if (note.notebook_id === delete_note.notebook_id) {
                        found = true
                        this.view_note(note)
                        break
                    }
                }

                if (!found) {
                    // No more notes in this notebook.
                    this.active_note_id = null
                    window.location.hash = '#'
                }

                // Delete note from notes array
                let delete_note_index = this.notes.findIndex(note => note.id === delete_note.id)
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

        /**
         * View notebook - show notes for the active notebook.
         *
         */
        view_notebook: function(notebook) {
            this.active_view_label = null
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
            // Update <title>
            document.title = this.get_note_title(note) + ' | Dopenote'

            // Update Backend
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
        get_note_title: function(note) {
            let prepend = '';

            if (this.active_view_label) {
                // View: Starred / trash.
                // Prepend the notebook title.
                let notebook = this.getNotebookByID(note.notebook_id)
                prepend = notebook.title + ': '
            }

            if (note.title.length) {
                return prepend + note.title
            }

            return prepend + '(untitled)'
        },

        /**
         * Get class for the 'star' button
         *
         */
        getStarClass: function(note) {
            return {
                'star': true,
                'starred': note && note.starred
            }
        },

        // TODO
        edit_notebook: function(notebook) {
            console.log('Todo: edit notebook: ' + notebook.id)
        },

        /**
         * Load a note from the URL hash note-ID, eg. '#/note/123'
         *
         */
        load_note_from_hash: function() {
            // Get note ID from URL hash
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
            } else if (view = this.getViewByLabel(split[1])) {
                // It's a view (starred, trash)
                this.set_view(view)
            }
        },

        /**
         * Triggered when hash changes.
         * Used for when going back/forward in the browser.
         *
         */
        onhashchange: function() {
            this.load_note_from_hash();
        },

        /**
         * When click a view (starred, trash)
         *
         */
        set_view: function(view) {
            this.active_notebook_id = null
            this.active_view_label = view.label
            window.location.hash = '#/' + view.label
        },

        /**
         * Filter function
         * Whether to show the note in the sidebar or note
         * Depending on which view/notebook is active.
         *
         */
        render_note_in_list: function(note) {
            if (this.active_view_label) {
                // View
                if (this.active_view_label == 'starred') {
                    // Starred
                    return note.starred
                }
                if (this.active_view_label == 'trash') {
                    // Trash (todo)
                    return note.deleted_at
                }
            }

            // Notebook
            return note.notebook_id === this.active_notebook_id
        },

    }
})
