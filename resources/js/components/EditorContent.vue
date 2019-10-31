<template>
  <div>
    <editor-menu-bar class="editor_toolbar" :editor="editor" v-slot="{ commands, isActive }">
      <div id="editor_menu_bar">
        <button tabindex="-1" :class="{ 'is-active': isActive.bold() }" @click="commands.bold()">
          <i class="fas fa-bold"></i>
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.italic() }" @click="commands.italic()">
          <i class="fas fa-italic"></i>
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.underline() }" @click="commands.underline()">
          <i class="fas fa-underline"></i>
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.strike() }" @click="commands.strike()">
          <i class="fas fa-strikethrough"></i>
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.heading({ level: 1 }) }" @click="commands.heading({ level: 1 })">
          H1
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.heading({ level: 2 }) }" @click="commands.heading({ level: 2 })">
          H2
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.heading({ level: 3 }) }" @click="commands.heading({ level: 3 })">
          H3
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.heading({ level: 4 }) }" @click="commands.heading({ level: 4 })">
          H4
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.link() }" @click="commands.link()">
          <i class="fas fa-link"></i>
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.code() }" @click="commands.code()">
          <i class="fas fa-code"></i>
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.horizontal_rule() }" @click="commands.horizontal_rule()">
          _
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.blockquote() }" @click="commands.blockquote()">
          <i class="fas fa-quote-right"></i>
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.code_block() }" @click="commands.code_block()">
          Code
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.bullet_list() }" @click="commands.bullet_list()">
          <i class="fas fa-list"></i>
        </button>

        <button tabindex="-1" :class="{ 'is-active': isActive.ordered_list() }" @click="commands.ordered_list()">
          <i class="fas fa-list-ol"></i>
        </button>
      </div>
    </editor-menu-bar>
    <editor-content :editor="editor" id="editor_content" ref="editor_content" autocomplete="off" :key="key" />
  </div>
</template>

<script>
import javascript from 'highlight.js/lib/languages/javascript'
import css from 'highlight.js/lib/languages/css'
import json from 'highlight.js/lib/languages/json'
import php from 'highlight.js/lib/languages/php'
import { Editor, EditorContent, EditorMenuBar } from 'tiptap'
import {
  Blockquote,
  CodeBlockHighlight,
  HardBreak,
  Heading,
  OrderedList,
  BulletList,
  ListItem,
  TodoItem,
  TodoList,
  Bold,
  Code,
  Italic,
  Link,
  Strike,
  Underline,
  History,
  HorizontalRule,
} from 'tiptap-extensions'

export default {
  components: {
    EditorMenuBar,
    EditorContent,
  },
  methods: {
  },
  data() {
    return {
      key: 1,
      editor: new Editor({
        extensions: [
          new Blockquote(),
          new CodeBlockHighlight({
            languages: {
              javascript,
              css,
              json,
              php,
            },
          }),
          new HardBreak(),
          new Heading({ levels: [1, 2, 3, 4] }),
          new BulletList(),
          new OrderedList(),
          new ListItem(),
          new TodoItem(),
          new TodoList(),
          new Bold(),
          new Code(),
          new Italic(),
          new Link(),
          new Strike(),
          new Underline(),
          new HorizontalRule(),
          // new History(), // disabled because of bug when changing notes.
        ],
        // Apparently we need to add <pre><code> tags for code highlight to work on page load.
        content: `<pre><code>Loading...</code></pre>`,
      }),
    }
  },
  beforeDestroy() {
    this.editor.destroy()
  },
}
</script>
