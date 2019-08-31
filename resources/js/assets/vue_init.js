window.Vue = require('vue');
window.axios = require('axios');

// Import components
// vue-simple-context-menu: Used for renaming and deleting notebooks in the sidebar.
import 'vue-simple-context-menu/dist/vue-simple-context-menu.css'
import VueSimpleContextMenu from 'vue-simple-context-menu'

// Draggable: Used for sorting notebooks in the sidebar.
import draggable from 'vuedraggable'


// Views (starred, trash)
app_data.active_view_label = null;
app_data.views = [
    // Starred
    {
        title: 'Starred',
        label: 'starred',
    },

    // Trash
    {
        title: 'Trash',
        label: 'trash',
    },
]


// Notebook context menu array
app_data.notebook_context_menu = [
    {
        name: "Rename Notebook",
        method: "render_rename_notebook",
    },
    {
        name: "Delete Notebook",
        method: "confirm_delete_notebook",
    },
]


var vueApp = new Vue({
    el: "#app",
    components: {
    	'vue-simple-context-menu': VueSimpleContextMenu,
    	draggable,
    },
    data: app_data,

    mounted: function() {
        this.sort_notes()
        this.sort_notebooks()
        this.load_note_from_hash();

        // On hash change
        window.onhashchange = this.onhashchange
    },

    methods: {
        /**
         * Called when the editor is loaded and ready
         *
         */
        editor_init: function() {
            let editor = tinymce.get('editor')

            // Set editor to disabled if note is deleted.
            this.toggle_editor_disabled(this.getActiveNote())

            // Update content backend on change.
            var self = this
            editor.on('keyup change redo undo', function(e) {
                if (self.getActiveNote().deleted_at === null) {
                    // Only allow update content if note is not deleted.
                    vueApp.set_content(vueApp.getActiveNote(), editor.getContent())
                }
            });
        },

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
         * Sort notebooks
         *
         */
        sort_notebooks: function() {
            // Sort by 'sort_order'
            this.notebooks.sort(function(a,b) {
                return a.sort_order > b.sort_order ? 1 : -1
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
                'notebook_link': true,
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

            // Set active view/notebook
            if (note.deleted_at !== null) {
                this.active_view_label = 'trash';
            }
            else if (!this.active_view_label) {
                this.active_notebook_id = note.notebook_id
            }

            document.title = this.get_note_title(note) + ' | Dopenote'
            window.location.hash = '#/note/' + note.id

            if (typeof tinymce !== 'undefined') {
                if (tinymce.get('editor') !== null) {
                    // "tinymce" variable is unset first time page loads.
                    tinymce.get('editor').setContent(note.content)
                }
            }

            this.toggle_editor_disabled(note)
        },

        /**
         * Toggle wether the editor should be editable or not.
         *
         */
        toggle_editor_disabled: function(note) {
            // Disable editor (if note is deleted)
            let allow_edit_body = note.deleted_at ? 'false' : 'true'

            if (tinymce.get('editor') === null) {
                // tinymce is not initialized yet, this only happens on pageload.
                // The 'editor_init' method will take care of that.
                return
            }

            tinymce.get('editor').getBody().setAttribute('contenteditable', allow_edit_body);
        },

        /**
         * Delete a note.
         *
         */
        delete_note: function(delete_note) {
            // Confirm
            if (!confirm('Are you sure you want to delete this note?')) {
                return
            }

            this.waiting_for_ajax = true

            axios
            .post('/note/' + delete_note.id + '/delete')
            .then(response => {
                this.waiting_for_ajax = false

                this.view_note_after_deletion(delete_note)

                delete_note.deleted_at = response.data.deleted_at
                // this.toggle_editor_disabled(delete_note)
            })
        },

        /**
         * Change active note to the first active note in current notebook.
         * Used after deleting a note.
         *
         */
        view_note_after_deletion: function(delete_note) {
            let found = false

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
                // No active notes in this notebook.
                this.active_note_id = null
                window.location.hash = '#'
            }
        },

        /**
         * Restore a deleted note.
         *
         */
        restore_note: function(note) {
            if (!note.deleted_at) {
                // Not deleted.
                alert('Cant restore a note thats not deleted.')
                return
            }

            this.waiting_for_ajax = true

            axios
            .post('/note/' + note.id + '/restore')
            .then(response => {
                this.waiting_for_ajax = false

                // If note's notebook is deleted, then find a new notebook.
                if (!this.getNotebookByID(note.notebook_id)) {
                    // Set notebook_id (TODO: Do it backend.)
                    this.set_notebook_id_on_note(note, this.notebooks[0].id)
                }

                // Restore in vue.
                note.deleted_at = null
            })
        },

        /**
         * Permanently delete a note
         *
         */
        perm_delete_note: function(note) {
            // Confirm
            if (!confirm('Are you sure you want to permanently delete this note?\r\nThis action CANNOT be undone.')) {
                return
            }

            this.waiting_for_ajax = true

            axios
            .post('/note/' + note.id + '/perm_delete')
            .then(response => {
                this.waiting_for_ajax = false

                // Remove note from array.
                let note_index = this.notes.indexOf(note)
                this.notes.splice(note_index, 1)

                this.view_note_after_deletion(note)

                // This should not be needed anymore.
                // this.toggle_editor_disabled(note)
            })
        },

        set_notebook_id_on_note: function(note, notebook_id) {
            this.waiting_for_ajax = true

            axios
            .post('/note/' + note.id + '/set_notebook', {
                notebook_id: notebook_id
            })
            .then(response => {
                this.waiting_for_ajax = false

                note.notebook_id = notebook_id
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

                note.deleted_at = null
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
            if (note.deleted_at) {
                alert('Cannot edit the title of a deleted note.')
                return
            }

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
                // Prepend the notebook title (if not deleted).
                let notebook = this.getNotebookByID(note.notebook_id)
                if (notebook) {
                    prepend = notebook.title + ': '
                }
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
                'starred': note && note.starred,
            }
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
                var note = this.getNoteByID(note_id)
                if (note) {
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

        get_note_list: function() {
            return this.notes.filter(this.render_note_in_list)
        },

        get_empty_note_list_text: function() {
            if (this.active_view_label === 'starred') {
                return 'No starred notes'
            }
            if (this.active_view_label === 'trash') {
                return 'No deleted notes'
            }

            return 'Empty.'
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
                    if (note.deleted_at) {
                        return false
                    }

                    return note.starred
                }
                if (this.active_view_label == 'trash') {
                    return note.deleted_at
                }
            }

            // Don't render deleted.
            if (note.deleted_at) {
                return false
            }

            // Only render notes for active notebook.
            return note.notebook_id === this.active_notebook_id
        },

        /**
         * Triggered when right clicking a notebook in the sidebar.
         *
         */
        notebook_context_menu_show: function(event, item) {
            this.$refs.vueSimpleContextMenu1.showMenu(event, item)

        },

        /**
         * Method is triggered when a context menu item is clicked.
         *
         */
        notebook_context_menu_action: function(event) {
            // Call this option's 'method' with the notebook. (event.item = notebook object)
            // See the 'notebook_context_menu' array.
            this[event.option.method](event.item)
        },

        /**
         * Prompt for renaming a notebook.title
         *
         */
        render_rename_notebook: function(notebook) {
            let new_title = prompt("Rename notebook", notebook.title)
            notebook.title = new_title

            // Update backend
            this.waiting_for_ajax = true
            axios.post('/notebook/' + notebook.id + '/rename', {
                title: new_title
            })
            .then(response => {
                this.waiting_for_ajax = false
            })
        },

        /**
         * Confirm and delete notebook
         *
         */
        confirm_delete_notebook: function(notebook) {
            if (!confirm('Are you sure want to delete this notebook (' + notebook.title + ')?')) {
                return
            }

            this.waiting_for_ajax = true

            axios.post('/notebook/' + notebook.id + '/delete')
            .then(response => {
                this.waiting_for_ajax = false

                // Delete notes in this notebook.
                // They are already deleted in backend.
                this.notes.forEach(function(note) {
                    if (response.data.notes.indexOf(note.id) !== -1) {
                        note.deleted_at = response.data.deleted_at
                    }
                })

                // Delete notebook from notebooks array
                let index = this.notebooks.findIndex(nb => nb.id === notebook.id)
                this.notebooks.splice(index, 1)
            })
        },

        /**
         * Event triggered when Notebook sort_order has changed.
         *
         */
        notebooks_draggable_change: function(event) {
            this.waiting_for_ajax = true

            axios
            .post('/notebook/update_sort_order', {
                old_index: event.oldIndex,
                new_index: event.newIndex,
            })
            .then(response => {
                this.waiting_for_ajax = false
            })
        },

    }
});


// vueApp.use(VueDraggable);

export default vueApp;
