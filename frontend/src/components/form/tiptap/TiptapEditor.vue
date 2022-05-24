<template>
  <div class="editor">
    <bubble-menu
      v-if="withExtensions"
      :editor="editor"
      :tippy-options="{ maxWidth: 'none' }">
      <v-toolbar short>
        <v-item-group class="v-btn-toggle v-btn-toggle--dense">
          <v-btn
            :class="
              editor.isActive('heading', { level: 1 })
                ? 'v-item--active v-btn--active'
                : ''
            "
            @click="editor.chain().focus().toggleHeading({ level: 1 }).run()">
            <v-icon>mdi-format-header-1</v-icon>
          </v-btn>
          <v-btn
            :class="
              editor.isActive('heading', { level: 2 })
                ? 'v-item--active v-btn--active'
                : ''
            "
            @click="editor.chain().focus().toggleHeading({ level: 2 }).run()">
            <v-icon>mdi-format-header-2</v-icon>
          </v-btn>
          <v-btn
            :class="
              editor.isActive('heading', { level: 3 })
                ? 'v-item--active v-btn--active'
                : ''
            "
            @click="editor.chain().focus().toggleHeading({ level: 3 }).run()">
            <v-icon>mdi-format-header-3</v-icon>
          </v-btn>
        </v-item-group>
        <div class="mx-1" />
        <v-item-group class="v-btn-toggle v-btn-toggle--dense" multiple>
          <v-btn
            :class="editor.isActive('bold') ? 'v-item--active v-btn--active' : ''"
            @click="editor.chain().focus().toggleBold().run()">
            <v-icon>mdi-format-bold</v-icon>
          </v-btn>
          <v-btn
            :class="editor.isActive('italic') ? 'v-item--active v-btn--active' : ''"
            @click="editor.chain().focus().toggleItalic().run()">
            <v-icon>mdi-format-italic</v-icon>
          </v-btn>
          <v-btn
            :class="editor.isActive('underline') ? 'v-item--active v-btn--active' : ''"
            @click="editor.chain().focus().toggleUnderline().run()">
            <v-icon>mdi-format-underline</v-icon>
          </v-btn>
          <v-btn
            :class="editor.isActive('strike') ? 'v-item--active v-btn--active' : ''"
            @click="editor.chain().focus().toggleStrike().run()">
            <v-icon>mdi-format-strikethrough</v-icon>
          </v-btn>
        </v-item-group>
      </v-toolbar>
    </bubble-menu>
    <editor-content class="editor__content" :editor="editor" />
  </div>
</template>
<script>
import { Editor, EditorContent, BubbleMenu } from '@tiptap/vue-2'
import Document from '@tiptap/extension-document'
import Paragraph from '@tiptap/extension-paragraph'
import Text from '@tiptap/extension-text'
import BulletList from '@tiptap/extension-bullet-list'
import HardBreak from '@tiptap/extension-hard-break'
import Heading from '@tiptap/extension-heading'
import ListItem from '@tiptap/extension-list-item'
import OrderedList from '@tiptap/extension-ordered-list'
import Bold from '@tiptap/extension-bold'
import Italic from '@tiptap/extension-italic'
import Strike from '@tiptap/extension-strike'
import Underline from '@tiptap/extension-underline'
import History from '@tiptap/extension-history'
import Placeholder from '@tiptap/extension-placeholder'

export default {
  name: 'TiptapEditor',
  components: {
    EditorContent,
    BubbleMenu
  },
  props: {
    value: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: ''
    },
    withExtensions: {
      type: Boolean,
      default: false
    },
    editable: {
      type: Boolean,
      default: true
    }
  },
  data () {
    const placeholder = Placeholder.configure({
      emptyEditorClass: 'is-editor-empty',
      emptyNodeClass: 'is-empty',
      emptyNodeText: '',
      showOnlyWhenEditable: true,
      showOnlyCurrent: true
    })
    const extensions = [Document, Paragraph, Text, placeholder]
    if (this.withExtensions) {
      extensions.push(
        ...[
          History,
          Bold,
          Italic,
          Underline,
          Strike,
          ListItem,
          BulletList,
          OrderedList,
          Heading.configure({ levels: [1, 2, 3] }),
          HardBreak
        ]
      )
    }

    return {
      editor: new Editor({
        extensions: extensions,
        content: this.value,
        onUpdate: this.onUpdate,
        editable: this.editable
      }),
      placeholderExtension: placeholder,
      regex: {
        emptyParagraph: /<p><\/p>/,
        lineBreak1: /<br>/g,
        lineBreak2: /<br\/>/g
      }
    }
  },
  computed: {
    html () {
      // Replace some Tags, to be compatible with backend HTMLPurifier
      return this.editor
        .getHTML()
        .replace(this.regex.emptyParagraph, '')
        .replace(this.regex.lineBreak1, '<br />')
        .replace(this.regex.lineBreak1, '<br />')
    }
  },
  watch: {
    value (val) {
      // Be careful to only use setContent when absolutely necessary, because it resets the user's cursor to the end
      // of the input field
      if (val !== this.html) {
        this.editor.commands.setContent(val)
      }
    },
    placeholder: {
      immediate: true,
      handler (val) {
        this.placeholderExtension.options.emptyNodeText = val
      }
    },
    editable () {
      this.editor.setOptions({
        editable: this.editable
      })
    }
  },
  methods: {
    focus () {
      this.editor.commands.focus()
    },
    onUpdate () {
      this.$emit('input', this.html)
    }
  }
}
</script>

<style scoped>
div.editor >>> p.is-editor-empty:first-child::before {
  content: attr(data-empty-text);
  float: left;
  color: #8b8b8b;
  pointer-events: none;
  height: 0;
}

div.editor {
  -webkit-box-flex: 1;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
  padding-top: 4px;
  max-width: 100%;
  min-width: 0;
  width: 100%;
}

div.editor >>> .editor__content {
  -webkit-box-flex: 1;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
}

div.editor >>> .editor__content .ProseMirror {
  border: 0 !important;
  box-shadow: none !important;
  outline: none;
  color: rgba(0, 0, 0, 0.87);
  line-height: 1.5;
}

div.editor >>> .editor__content .ProseMirror p {
  letter-spacing: -0.011em;
}
div.editor >>> .editor__content .ProseMirror p,
div.editor >>> .editor__content .ProseMirror ol,
div.editor >>> .editor__content .ProseMirror ul {
  margin-bottom: 6px;
}
div.editor >>> .editor__content .ProseMirror h1 {
  margin-top: 18px;
  margin-bottom: 6px;
}
div.editor >>> .editor__content .ProseMirror h2 {
  margin-top: 15px;
  margin-bottom: 6px;
}
div.editor >>> .editor__content .ProseMirror h3 {
  margin-top: 12px;
  margin-bottom: 6px;
}
div.editor >>> .editor__content .ProseMirror :first-child {
  margin-top: 0;
}
div.editor >>> .editor__content .ProseMirror li p {
  margin-bottom: 3px;
}
div.editor >>> .editor__content .ProseMirror li p:not(:last-child) {
  margin-bottom: 0;
}
</style>
