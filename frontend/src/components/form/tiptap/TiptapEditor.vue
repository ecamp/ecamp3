<template>
  <div
    class="editor"
    :class="{
      'editor--editable': editable,
      'editor--link-cursor': hoverCursor,
    }"
  >
    <bubble-menu
      v-if="withExtensions"
      ref="bubbleMenu"
      :editor="editor"
      :should-show="shouldShow"
      :tippy-options="{ maxWidth: 'none' }"
    >
      <div class="elevation-4 ec-tiptap-toolbar white">
        <v-toolbar class="elevation-0 ec-tiptap-toolbar--first" dense color="transparent">
          <TiptapToolbarButton
            icon="mdi-format-bold"
            :class="editor.isActive('bold') ? 'v-item--active v-btn--active' : ''"
            @click="editor.chain().focus().toggleBold().run()"
          />
          <TiptapToolbarButton
            icon="mdi-format-italic"
            :class="editor.isActive('italic') ? 'v-item--active v-btn--active' : ''"
            @click="editor.chain().focus().toggleItalic().run()"
          />
          <TiptapToolbarButton
            icon="mdi-format-underline"
            :class="editor.isActive('underline') ? 'v-item--active v-btn--active' : ''"
            @click="editor.chain().focus().toggleUnderline().run()"
          />
          <TiptapToolbarButton
            icon="mdi-format-strikethrough"
            :class="editor.isActive('strike') ? 'v-item--active v-btn--active' : ''"
            @click="editor.chain().focus().toggleStrike().run()"
          />

          <div class="d-none d-sm-contents">
            <v-divider vertical class="mx-1" />

            <TiptapToolbarButton
              icon="mdi-format-list-bulleted"
              :class="editor.isActive('bulletList') ? 'v-item--active v-btn--active' : ''"
              @click="editor.chain().focus().toggleBulletList().run()"
            />
            <TiptapToolbarButton
              icon="mdi-format-list-numbered"
              :class="
                editor.isActive('orderedList') ? 'v-item--active v-btn--active' : ''
              "
              @click="editor.chain().focus().toggleOrderedList().run()"
            />

            <template
              v-if="
                editor.can().sinkListItem('listItem') ||
                editor.can().liftListItem('listItem')
              "
            >
              <v-divider vertical class="mx-1" />
              <TiptapToolbarButton
                icon="mdi-format-indent-decrease"
                :disabled="!editor.can().liftListItem('listItem')"
                @click="editor.chain().focus().liftListItem('listItem').run()"
              />
              <TiptapToolbarButton
                icon="mdi-format-indent-increase"
                :disabled="!editor.can().sinkListItem('listItem')"
                @click="editor.chain().focus().sinkListItem('listItem').run()"
              />
            </template>
          </div>
        </v-toolbar>
        <v-divider class="ec-tiptap-toolbar__mobile-divider" />
        <v-toolbar
          class="elevation-0 ec-tiptap-toolbar--second"
          dense
          color="transparent"
        >
          <TiptapToolbarButton
            icon="mdi-format-list-bulleted"
            :class="editor.isActive('bulletList') ? 'v-item--active v-btn--active' : ''"
            @click="editor.chain().focus().toggleBulletList().run()"
          />
          <TiptapToolbarButton
            icon="mdi-format-list-numbered"
            :class="editor.isActive('orderedList') ? 'v-item--active v-btn--active' : ''"
            @click="editor.chain().focus().toggleOrderedList().run()"
          />

          <template
            v-if="
              editor.can().sinkListItem('listItem') ||
              editor.can().liftListItem('listItem')
            "
          >
            <v-divider vertical class="mx-1" />
            <TiptapToolbarButton
              icon="mdi-format-indent-decrease"
              :disabled="!editor.can().liftListItem('listItem')"
              @click="editor.chain().focus().liftListItem('listItem').run()"
            />
            <TiptapToolbarButton
              icon="mdi-format-indent-increase"
              :disabled="!editor.can().sinkListItem('listItem')"
              @click="editor.chain().focus().sinkListItem('listItem').run()"
            />
          </template>
        </v-toolbar>
      </div>
    </bubble-menu>
    <editor-content class="editor__content" :editor="editor" />
  </div>
</template>
<script>
import { BubbleMenu, Editor, EditorContent } from '@tiptap/vue-2'
import Document from '@tiptap/extension-document'
import Paragraph from '@tiptap/extension-paragraph'
import Text from '@tiptap/extension-text'
import BulletList from '@tiptap/extension-bullet-list'
import HardBreak from '@tiptap/extension-hard-break'
import ListItem from '@tiptap/extension-list-item'
import OrderedList from '@tiptap/extension-ordered-list'
import Bold from '@tiptap/extension-bold'
import Italic from '@tiptap/extension-italic'
import Strike from '@tiptap/extension-strike'
import Underline from '@tiptap/extension-underline'
import History from '@tiptap/extension-history'
import Placeholder from '@tiptap/extension-placeholder'
import TiptapToolbarButton from '@/components/form/tiptap/TiptapToolbarButton.vue'
import {
  AutoLinkDecoration,
  AutoLinkKey,
} from '@/components/form/tiptap/AutoLinkDecoration.js'
import { isTextSelection } from '@tiptap/core'

export default {
  name: 'TiptapEditor',
  components: {
    TiptapToolbarButton,
    EditorContent,
    BubbleMenu,
  },
  props: {
    value: {
      type: String,
      default: '',
    },
    placeholder: {
      type: String,
      default: '',
    },
    withExtensions: {
      type: Boolean,
      default: false,
    },
    editable: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    const placeholder = Placeholder.configure({
      emptyEditorClass: 'is-editor-empty',
      emptyNodeClass: 'is-empty',
      emptyNodeText: '',
      showOnlyWhenEditable: true,
      showOnlyCurrent: true,
      placeholder: () => {
        return this.placeholder
      },
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
          AutoLinkDecoration,
          // headings currently disabled (see issue #2657)
          HardBreak,
        ]
      )
    }

    return {
      hoverCursor: false,
      editor: new Editor({
        extensions: extensions,
        content: this.value,
        onUpdate: this.onUpdate,
        editable: this.editable,
      }),
      placeholderExtension: placeholder,
      regex: {
        emptyParagraph: /<p><\/p>/,
        lineBreak1: /<br>/g,
        lineBreak2: /<br\/>/g,
      },
      // copied from @tiptap/extension-bubble-menu
      shouldShow: ({ view, state, from, to }) => {
        const { doc, selection } = state
        const { empty } = selection

        // Sometime check for `empty` is not enough.
        // Doubleclick an empty paragraph returns a node size of 2.
        // So we check also for an empty text size.
        const isEmptyTextBlock =
          !doc.textBetween(from, to).length && isTextSelection(state.selection)

        // Don't show if selection is within of an autolink
        if (this.withExtensions) {
          const links = AutoLinkKey.getState(state).find(
            from,
            to,
            (decoration) => decoration.start <= from && to <= decoration.end
          )
          if (links.length) {
            return false
          }
        }

        // When clicking on an element inside the bubble menu the editor "blur" event
        // is called and the bubble menu item is focussed. In this case we should
        // consider the menu as part of the editor and keep showing the menu
        const isChildOfMenu = this.$refs.bubbleMenu.$el.contains(document.activeElement)

        const hasEditorFocus = view.hasFocus() || isChildOfMenu

        if (!hasEditorFocus || empty || isEmptyTextBlock || !this.editor.isEditable) {
          return false
        }

        return true
      },
    }
  },
  computed: {
    html() {
      // Replace some Tags, to be compatible with backend HTMLPurifier
      return this.editor
        .getHTML()
        .replace(this.regex.emptyParagraph, '')
        .replace(this.regex.lineBreak1, '<br />')
        .replace(this.regex.lineBreak2, '<br />')
    },
  },
  watch: {
    value(val) {
      // Be careful to only use setContent when absolutely necessary, because it resets the user's cursor to the end
      // of the input field
      if (val !== this.html) {
        this.editor.commands.setContent(val)
      }
    },
    editable() {
      this.editor.setOptions({
        editable: this.editable,
      })
    },
  },
  mounted() {
    document.addEventListener('keydown', this.specialKeyListeners, { passive: true })
    document.addEventListener('keyup', this.specialKeyListeners, { passive: true })
    document.addEventListener('contextmenu', this.specialMenuListeners, { passive: true })
  },
  beforeDestroy() {
    document.removeEventListener('keydown', this.specialKeyListeners)
    document.removeEventListener('keyup', this.specialKeyListeners)
    document.removeEventListener('contextmenu', this.specialMenuListeners)
  },
  methods: {
    focus() {
      this.editor.commands.focus()
    },
    onUpdate() {
      this.$emit('input', this.html)
    },
    specialKeyListeners(event) {
      this.hoverCursor = event.metaKey || event.ctrlKey
    },
    specialMenuListeners() {
      this.hoverCursor = false
    },
  },
}
</script>

<style scoped lang="scss">
div.editor:deep(div.ProseMirror) {
  word-wrap: anywhere;
}
div.editor:deep(p.is-editor-empty:first-child::before) {
  content: attr(data-placeholder);
  float: left;
  color: #8b8b8b;
  pointer-events: none;
  height: 0;
}

div.editor {
  -webkit-box-flex: 1;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
  padding-top: 6px;
  max-width: 100%;
  min-width: 0;
  width: 100%;
}

div.editor:deep(.ec-tiptap-toolbar) {
  border-radius: 6px;
}

.ec-tiptap-toolbar--second,
.ec-tiptap-toolbar__mobile-divider {
  display: block;
  @media #{map-get($display-breakpoints, 'sm-and-up')} {
    display: none;
  }
}

div.editor:deep(.ec-tiptap-toolbar--first .v-toolbar__content) {
  justify-content: space-between;
}

div.editor:deep(.ec-tiptap-toolbar .v-toolbar__content) {
  gap: 2px;
  padding: 0 4px;
  .v-btn {
    margin: 0;
  }
}

div.editor:deep(.editor__content) {
  -webkit-box-flex: 1;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
  max-width: 100%;
}

div.editor:deep(.editor__content .ProseMirror) {
  border: 0 !important;
  box-shadow: none !important;
  outline: none;
  color: rgba(0, 0, 0, 0.87);
  line-height: 1.5;
}

.theme--light.v-input--is-disabled div.editor:deep(.editor__content .ProseMirror) {
  color: rgba(0, 0, 0, 0.38);
}

div.editor:deep(.editor__content .ProseMirror p) {
  letter-spacing: -0.011em;
}
div.editor:deep(.editor__content .ProseMirror p),
div.editor:deep(.editor__content .ProseMirror ol),
div.editor:deep(.editor__content .ProseMirror ul) {
  margin-bottom: 6px;
}
div.editor:deep(.editor__content .ProseMirror h1) {
  margin-top: 18px;
  margin-bottom: 6px;
}
div.editor:deep(.editor__content .ProseMirror h2) {
  margin-top: 15px;
  margin-bottom: 6px;
}
div.editor:deep(.editor__content .ProseMirror h3) {
  margin-top: 12px;
  margin-bottom: 6px;
}
div.editor:deep(.editor__content .ProseMirror :first-child) {
  margin-top: 0;
}
div.editor:deep(.editor__content .ProseMirror li p) {
  margin-bottom: 3px;
}
div.editor:deep(.editor__content .ProseMirror li p:not(:last-child)) {
  margin-bottom: 0;
}
.editor.editor--editable:deep(.autolink) {
  cursor: text;
}
.editor.editor--link-cursor:deep(.autolink) {
  cursor: pointer;
}
</style>
