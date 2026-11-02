<template>
  <div
    class="editor"
    :class="{
      'editor--editable': editable,
      'editor--link-cursor': hoverCursor,
      'editor--images': imageCampUri && editable,
    }"
  >
    <bubble-menu
      v-if="withExtensions && everEditable"
      ref="bubbleMenu"
      plugin-key="editorBubbleMenu"
      :editor="editor"
      :should-show="shouldShow"
      class="z-10"
    >
      <div class="elevation-4 ec-tiptap-toolbar bg-white">
        <v-toolbar
          v-if="editor.isActive('image')"
          density="compact"
          color="transparent"
          class="elevation-0"
        >
          <TiptapToolbarButton
            icon="mdi-plus"
            :aria-label="$t('richTextImages.increase')"
            :title="$t('richTextImages.increase')"
            :disabled="imageWidth >= 2400"
            @mousedown.prevent
            @click="resizeImage(32)"
          />
          <TiptapToolbarButton
            icon="mdi-minus"
            :aria-label="$t('richTextImages.decrease')"
            :title="$t('richTextImages.decrease')"
            :disabled="imageWidth <= 32"
            @mousedown.prevent
            @click="resizeImage(-32)"
          />
          <TiptapToolbarButton
            icon="mdi-pencil"
            :aria-label="$t('richTextImages.alt')"
            :title="$t('richTextImages.alt')"
            @mousedown.prevent
            @click="openImageAltDialog"
          />
        </v-toolbar>
        <template v-else>
          <v-toolbar
            class="elevation-0 ec-tiptap-toolbar--first"
            density="compact"
            color="transparent"
          >
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
                :class="
                  editor.isActive('bulletList') ? 'v-item--active v-btn--active' : ''
                "
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
            density="compact"
            color="transparent"
          >
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
          </v-toolbar>
        </template>
      </div>
    </bubble-menu>
    <div v-if="imageCampUri && editable" class="editor__image-actions">
      <TiptapToolbarButton
        icon="mdi-image-plus"
        :height="28"
        :width="28"
        variant="text"
        class="editor__image-button"
        :aria-label="$t('richTextImages.add')"
        :title="$t('richTextImages.add')"
        @mousedown.prevent
        @click="chooseImages"
      />
      <input
        ref="imageInput"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        multiple
        hidden
        @click.stop
        @change="onImageFiles"
      />
      <span v-if="uploadingImages" role="status">{{
        $t('richTextImages.uploading')
      }}</span>
    </div>
    <div v-if="imageError" role="alert" class="text-error">{{ imageError }}</div>
    <DialogForm
      v-if="imageAltDialog"
      v-model="imageAltDialog"
      :title="$t('richTextImages.alt')"
      icon="mdi-pencil"
      :submit-action="saveImageAlt"
      :cancel-action="closeImageAltDialog"
      :submit-enabled="editable"
      max-width="480"
    >
      <v-text-field
        v-model="imageAltDraft"
        :label="$t('richTextImages.alt')"
        autofocus
        hide-details
      />
    </DialogForm>
    <editor-content class="editor__content" :editor="editor" />
  </div>
</template>
<script>
import { Editor, EditorContent } from '@tiptap/vue-3'
import { BubbleMenu } from '@tiptap/vue-3/menus'
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
import { createNodeFromContent, isTextSelection } from '@tiptap/core'
import axios from 'axios'
import { getEnv } from '@/environment.js'
import CampImage from './CampImage.js'
import ImageUploads, { insertImageFiles } from './ImageUploads.js'
import DialogForm from '@/components/dialog/DialogForm.vue'

function isSillyThingThatHappensWithTipTap(val) {
  return val && Object.prototype.hasOwnProperty.call(val, 'isTrusted')
}

export default {
  name: 'TiptapEditor',
  components: {
    DialogForm,
    TiptapToolbarButton,
    EditorContent,
    BubbleMenu,
  },
  props: {
    imageCampUri: { type: String, default: null },
    modelValue: {
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
    autofocus: {
      type: [Boolean, String, Number],
      default: false,
    },
  },
  emits: ['tiptapUpdate', 'focus', 'blur'],
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
    if (this.imageCampUri) extensions.push(CampImage, ImageUploads)
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
      imageAltDialog: false,
      imageAltDraft: '',
      imageAltBookmark: null,
      uploadingImages: 0,
      imageError: '',
      imageSelection: null,
      hoverCursor: false,
      everEditable: this.editable,
      editor: new Editor({
        extensions: extensions,
        content: this.modelValue,
        onUpdate: this.onUpdate,
        onTransaction: ({ transaction }) => {
          if (this.imageAltBookmark && !transaction.before.eq(transaction.doc)) {
            this.imageAltBookmark = this.imageAltBookmark.map(transaction.mapping)
          }
        },
        onFocus: (e) => this.$emit('focus', e),
        onBlur: (e) => this.$emit('blur', e),
        editable: this.editable,
        autofocus: this.autofocus,
        editorProps: {
          handlePaste: (_, event) => this.onImagePaste(event),
          handleDrop: (view, event, _slice, moved) => {
            if (
              moved ||
              !this.imageCampUri ||
              !this.editable ||
              !event.dataTransfer?.files.length
            )
              return false
            const position = view.posAtCoords({
              left: event.clientX,
              top: event.clientY,
            })?.pos
            event.preventDefault()
            this.uploadImages(
              event.dataTransfer.files,
              position ?? this.editor.state.selection.from
            )
            return true
          },
        },
      }),
      placeholderExtension: placeholder,
      regex: {
        emptyParagraph: /<p><\/p>/,
        lineBreak1: /<br>/g,
        lineBreak2: /<br\/>/g,
      },
      // copied from @tiptap/extension-bubble-menu
      // https://github.com/ueberdosis/tiptap/blob/64f36b8d93b437f2230d8538a8ecdb504842d5f8/packages/extension-bubble-menu/src/bubble-menu-plugin.ts#L195-L216
      // modifications:
      // - autolink adaption
      // - this.$refs.bubbleMenu instead of this.element
      shouldShow: ({ view, state, from, to }) => {
        const { doc, selection } = state
        const { empty } = selection

        // Sometime check for `empty` is not enough.
        // Doubleclick an empty paragraph returns a node size of 2.
        // So we check also for an empty text size.
        const isEmptyTextBlock =
          !doc.textBetween(from, to).length && isTextSelection(state.selection)

        // ===== START EDIT eCamp ======
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
        // ===== END   EDIT eCamp ======

        // When clicking on a element inside the bubble menu the editor "blur" event
        // is called and the bubble menu item is focussed. In this case we should
        // consider the menu as part of the editor and keep showing the menu
        // ===== START EDIT eCamp ======
        const isChildOfMenu = this.$refs.bubbleMenu?.$el.contains(document.activeElement)
        // ===== END   EDIT eCamp ======

        const hasEditorFocus = view.hasFocus() || isChildOfMenu

        if (!hasEditorFocus || empty || isEmptyTextBlock || !this.editor.isEditable) {
          return false
        }

        return true
      },
    }
  },
  computed: {
    imageSelected() {
      return this.editor.isActive('image')
    },
    imageWidth() {
      return Number(this.editor.getAttributes('image').width) || 640
    },
    html() {
      return this.getHtml()
    },
  },
  watch: {
    imageSelected(selected) {
      this.editor.view.dispatch(
        this.editor.state.tr.setMeta('editorBubbleMenu', {
          type: 'updateOptions',
          options: { options: { placement: selected ? 'top-start' : 'top' } },
        })
      )
      this.editor.view.dispatch(
        this.editor.state.tr.setMeta('editorBubbleMenu', 'updatePosition')
      )
    },
    modelValue(val) {
      // Be careful to only use setContent when absolutely necessary, because it resets the user's cursor to the end
      // of the input field
      if (val !== this.html && !isSillyThingThatHappensWithTipTap(val)) {
        const document = createNodeFromContent(val ?? '', this.editor.schema, {
          slice: false,
          parseOptions: this.editor.options.parseOptions,
        })
        if (document.eq(this.editor.state.doc)) return
        // we do not want to trigger onUpdate when modelValue is updated from outside
        this.editor.commands.setContent(val, { emitUpdate: false })
      }
    },
    editable() {
      this.everEditable = this.everEditable || this.editable
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
  beforeUnmount() {
    document.removeEventListener('keydown', this.specialKeyListeners)
    document.removeEventListener('keyup', this.specialKeyListeners)
    document.removeEventListener('contextmenu', this.specialMenuListeners)
    this.editor.destroy()
  },
  methods: {
    chooseImages() {
      this.imageSelection = this.editor.state.selection.from
      this.$refs.imageInput.click()
    },
    onImageFiles(event) {
      this.uploadImages(event.target.files, this.imageSelection)
      event.target.value = ''
    },
    onImagePaste(event) {
      if (!this.imageCampUri || !this.editable || !event.clipboardData?.files.length)
        return false
      event.preventDefault()
      this.uploadImages(event.clipboardData.files, this.editor.state.selection.from)
      return true
    },
    async uploadImages(files, position) {
      if (!files?.length) return
      this.imageError = ''
      if (
        Array.from(files).some(
          (file) =>
            !['image/jpeg', 'image/png', 'image/webp'].includes(file.type) ||
            file.size > 10485760
        )
      ) {
        this.imageError = this.$t('richTextImages.invalid')
        return
      }
      this.uploadingImages++
      try {
        await insertImageFiles(
          this.editor,
          files,
          position,
          async (file) => {
            const form = new FormData()
            form.append('file', file)
            const { data } = await axios.post(`${this.imageCampUri}/images`, form, {
              baseURL: getEnv().API_ROOT_URL,
              withCredentials: true,
              headers: { Accept: 'application/json' },
            })
            return { src: data.src, alt: '', width: Math.min(data.width, 640) }
          },
          this.$t('richTextImages.uploading')
        )
      } catch {
        this.imageError = this.$t('richTextImages.failed')
      } finally {
        this.uploadingImages--
      }
    },
    resizeImage(delta) {
      if (!this.editable || !this.editor.isActive('image')) return
      const position = this.editor.state.selection.from
      const currentWidth = Number(this.editor.getAttributes('image').width) || 640
      const width = Math.max(32, Math.min(2400, currentWidth + delta))
      this.editor
        .chain()
        .updateAttributes('image', { width })
        .setNodeSelection(position)
        .focus()
        .run()
    },
    openImageAltDialog() {
      if (!this.editable || !this.editor.isActive('image')) return
      this.imageAltBookmark = this.editor.state.selection.getBookmark()
      this.imageAltDraft = this.editor.getAttributes('image').alt ?? ''
      this.imageAltDialog = true
    },
    saveImageAlt() {
      const selection = this.imageAltBookmark?.resolve(this.editor.state.doc)
      if (this.editable && selection?.node?.type.name === 'image') {
        this.editor
          .chain()
          .setNodeSelection(selection.from)
          .updateAttributes('image', { alt: this.imageAltDraft })
          .setNodeSelection(selection.from)
          .run()
      }
      this.closeImageAltDialog()
    },
    closeImageAltDialog() {
      this.imageAltDialog = false
      this.imageAltBookmark = null
      if (!this.editor.isDestroyed) this.editor.commands.focus()
    },
    focus() {
      this.editor.commands.focus()
    },
    onUpdate() {
      this.$emit('tiptapUpdate', this.getHtml())
    },
    getHtml() {
      return this.editor
        .getHTML()
        .replace(this.regex.emptyParagraph, '')
        .replace(this.regex.lineBreak1, '<br />')
        .replace(this.regex.lineBreak2, '<br />')
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
@use 'vuetify/settings';
@use 'sass:map';

div.editor:deep(p.is-editor-empty:first-child::before) {
  content: attr(data-placeholder);
  float: left;
  color: #8b8b8b;
  pointer-events: none;
  height: 0;
}

div.editor {
  position: relative;
  -webkit-box-flex: 1;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
  padding-top: 6px;
  max-width: 100%;
  min-width: 0;
  width: 100%;
}

.editor__image-actions {
  position: absolute;
  top: 0;
  right: 0;
  z-index: 1;
  display: flex;
  align-items: center;
  gap: 4px;
}

.editor__image-button {
  opacity: 0.45;
}

.editor__image-button :deep(.v-icon) {
  font-size: 18px;
}

.editor__image-button:hover,
.editor__image-button:focus-visible {
  opacity: 1;
}

.editor--images :deep(.editor__content) {
  padding-inline-end: 32px;
}

div.editor:deep(.ec-tiptap-toolbar) {
  border-radius: 6px;
}

.ec-tiptap-toolbar--second,
.ec-tiptap-toolbar__mobile-divider {
  display: block;
  @media #{map.get(settings.$display-breakpoints, 'sm-and-up')} {
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
  overflow-wrap: anywhere;
}

div.editor:deep(.ProseMirror img) {
  max-width: 100%;
  height: auto;
}

div.editor:deep(.ProseMirror img.ProseMirror-selectednode) {
  outline: 2px solid currentcolor;
}

div.editor:deep(.image-upload-placeholder) {
  display: inline-block;
  padding: 4px 8px;
  background: #eee;
}

.v-theme--light.v-input--is-disabled div.editor:deep(.editor__content .ProseMirror) {
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
div.editor:deep(.editor__content .ProseMirror ol),
div.editor:deep(.editor__content .ProseMirror ul) {
  padding-left: 24px;
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
.editor.editor--editable {
  cursor: text;
}
.editor.editor--editable:deep(.autolink) {
  cursor: text;
}
.editor.editor--link-cursor:deep(.autolink) {
  cursor: pointer;
}
</style>
