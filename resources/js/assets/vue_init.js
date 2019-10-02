window.Vue = require('vue');

import Ajax from './Ajax.js'

// Import Vue components
// vue-simple-context-menu: Used for renaming and deleting notebooks in the sidebar.
import 'vue-simple-context-menu/dist/vue-simple-context-menu.css'
import VueSimpleContextMenu from 'vue-simple-context-menu'

// Draggable: Used for sorting notebooks in the sidebar.
import draggable from 'vuedraggable'

// Editor
import EditorContent from '../components/EditorContent.vue'

import Tooltip from 'vue-directive-tooltip';
import 'vue-directive-tooltip/dist/vueDirectiveTooltip.css';
Vue.use(Tooltip);

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
        'editor-content': EditorContent
    },
    data: app_data,

    mounted: function() {
        let self = this

        Ajax.before_post = function() {
            self.waiting_for_ajax = true
        }
        Ajax.after_post = function() {
            self.waiting_for_ajax = false
        }

        this.editor = this.$refs.editor.editor
        this.sort_notes()
        this.sort_notebooks()
        this.load_note_from_hash()
        this.editor_events()

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

            this.editor.setContent(note.content)

            this.toggle_editor_disabled(note)
        },

        /**
         * Toggle wether the editor should be editable or not.
         *
         */
        toggle_editor_disabled: function(note) {
            // Disable editor (if note is deleted)
            let allow_edit_body = note.deleted_at ? false : true

            this.editor.setOptions({
                editable: allow_edit_body,
            })
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

            let self = this

            Ajax.post({
                url: '/note/' + delete_note.id + '/delete',
                success: function(response) {
                    self.view_note_after_deletion(delete_note)
                    delete_note.deleted_at = response.data.deleted_at
                }
            })
        },

        /**
         * Change active note to the first active note in current notebook.
         * Used after deleting a note.
         *
         */
        view_note_after_deletion: function(delete_note) {
            let found = false

            for (let note_id in this.notes) {
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

            let self = this

            Ajax.post({
                url: '/note/' + note.id + '/restore',
                success: function(response) {
                    // If note's notebook is deleted, then find and set a new notebook.
                    if (!self.getNotebookByID(note.notebook_id)) {
                        // Set notebook_id (TODO: Do it backend.)
                        self.set_notebook_id_on_note(note, self.notebooks[0].id)
                    }

                    // Restore in vue.
                    note.deleted_at = null
                }
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

            let self = this

            Ajax.post({
                url: '/note/' + note.id + '/perm_delete',
                success: function(response) {
                    // Remove note from array.
                    let note_index = self.notes.indexOf(note)
                    self.notes.splice(note_index, 1)
                    self.view_note_after_deletion(note)
                }                
            })
        },

        set_notebook_id_on_note: function(note, notebook_id) {
            let self = this

            Ajax.post({
                url: '/note/' + note.id + '/set_notebook',
                data: {
                    notebook_id: notebook_id,
                },
                success: function(response) {
                    note.notebook_id = notebook_id
                }                
            })
        },

        /**
         * Create a new note.
         *
         */
        create_note: function() {
            let self = this

            Ajax.post({
                url: '/note/create',
                data: {
                    notebook_id: self.active_notebook_id
                },
                success: function(response) {
                    let note = response.data.note

                    note.deleted_at = null
                    self.notes.push(note)
                    self.sort_notes()
                    self.view_note(note)

                    self.$refs.note_title.focus()
                }                
            })
        },

        /**
         * Create a new notebook.
         *
         */
        create_notebook: function() {
            let self = this

            Ajax.post({
                url: '/notebook/create',
                success: function(response) {
                    let notebook = response.data.notebook
                    self.notebooks.push(notebook)
                }                
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
            let self = this

            Ajax.post({
                url: '/note/' + note.id + '/toggle_star',
                success: function(response) {
                    note.starred = response.data.note.starred
                    note.updated_at = response.data.note.updated_at
                    self.sort_notes()
                }
            })
        },

        /**
         * Update note (changes note.updated_at)
         *
         */
        set_content: function(note, content) {
            let self = this

            Ajax.post({
                url: '/note/' + note.id + '/set_content',
                data: {
                    content: content,
                },
                success: function(response) {
                    note.content = content
                    note.updated_at = response.data.note.updated_at
                    self.sort_notes()
                }
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

            let self = this

            Ajax.post({
                url: '/note/' + note.id + '/set_title',
                data: {
                    title: note.title
                },
                success: function(response) {
                    note.updated_at = response.data.note.updated_at
                    self.sort_notes()
                }
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
            let view

            if (hash.length === 0) {
                // Use 'active_note_id' sent from backend
                this.view_note(this.getActiveNote())
            } else if (split[1] == 'note') {
                // Render note.
                let note_id = parseInt(split[2])
                let note = this.getNoteByID(note_id)
                if (note) {
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
            let self = this
            Ajax.post({
                url: '/notebook/' + notebook.id + '/rename',
                data: {
                    title: new_title
                },
                success: function(response) {
                }
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

            let self = this
            Ajax.post({
                url: '/notebook/' + notebook.id + '/delete',
                success: function(response) {
                    // Delete notes in this notebook.
                    // They are already deleted in backend.
                    self.notes.forEach(function(note) {
                        if (response.data.notes.indexOf(note.id) !== -1) {
                            note.deleted_at = response.data.deleted_at
                        }
                    })

                    // Delete notebook from notebooks array
                    let index = self.notebooks.findIndex(nb => nb.id === notebook.id)
                    self.notebooks.splice(index, 1)
                }
            })
        },

        /**
         * Event triggered when Notebook sort_order has changed.
         *
         */
        notebooks_draggable_change: function(event) {
            let self = this

            Ajax.post({
                url: '/notebook/update_sort_order',
                data: {
                    old_index: event.oldIndex,
                    new_index: event.newIndex,
                }
            })
        },

        /**
         * Handle events on editor and title fields
         *
         */
        editor_events: function() {
            let self = this

            // Update content backend on change.
            self.editor.on('update', ({ getHTML }) => {
                self.set_content(self.getActiveNote(), getHTML())
            })

            // On key down on title
            document.querySelector('.note-title input').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    // Focus on editor'
                    self.editor.focus()
                }
            });
        },

    }
});


// vueApp.use(VueDraggable);

export default vueApp;
