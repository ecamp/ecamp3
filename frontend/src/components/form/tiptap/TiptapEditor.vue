<script>

import { Editor, EditorContent, EditorMenuBubble } from 'tiptap'
import {
  BulletList,
  HardBreak,
  Heading,
  ListItem,
  OrderedList,
  Bold,
  Italic,
  Strike,
  Underline,
  History,
  Placeholder
} from 'tiptap-extensions'
import { VBtn, VIcon, VItemGroup, VSpacer, VToolbar } from 'vuetify/lib'

export default {
  name: 'TiptapEditor',
  components: {
    VBtn,
    VIcon,
    VItemGroup,
    VSpacer,
    VToolbar,
    EditorContent,
    EditorMenuBubble
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
    }
  },
  data () {
    const extensions = [
      new Placeholder({
        emptyEditorClass: 'is-editor-empty',
        emptyNodeClass: 'is-empty',
        emptyNodeText: '',
        showOnlyWhenEditable: true,
        showOnlyCurrent: true
      })
    ]
    if (this.withExtensions) {
      extensions.push(...[
        new History(),
        new Bold(),
        new Italic(),
        new Underline(),
        new Strike(),
        new ListItem(),
        new BulletList(),
        new OrderedList(),
        new Heading({ levels: [1, 2, 3] }),
        new HardBreak()
      ])
    }

    return {
      emitAfterOnUpdate: false,
      editor: new Editor({
        extensions: extensions,
        content: this.value,
        onUpdate: this.onUpdate,
        onFocus: this.onFocus,
        onBlur: this.onBlur
      }),
      regex: {
        emptyParagraph: new RegExp('<p></p>'),
        lineBreak1: new RegExp('<br>', 'g'),
        lineBreak2: new RegExp('<br/>', 'g')
      },
      lastSelection: null
    }
  },
  watch: {
    value (val) {
      if (this.emitAfterOnUpdate) {
        this.emitAfterOnUpdate = false
        return
      }
      this.lastSelection = null
      this.editor.setContent(val)
    },
    placeholder: {
      immediate: true,
      handler (val) {
        this.editor.extensions.options.placeholder.emptyNodeText = val
      }
    }
  },
  methods: {
    focus () {
      this.editor.focus()
    },
    onUpdate (info) {
      let output = info.getHTML()

      // Replace some Tags, to be compatible with backend HTMLPurifier
      output = output.replace(this.regex.emptyParagraph, '')
      output = output.replace(this.regex.lineBreak1, '<br />')
      output = output.replace(this.regex.lineBreak1, '<br />')

      this.emitAfterOnUpdate = true
      this.$emit('input', output, info)
    },
    onFocus (e) {
      this.$emit('focus', e)

      const sel = document.getSelection()
      if (sel.rangeCount === 1) {
        const range = sel.getRangeAt(0)
        if (this.lastSelection === null) {
          const div = this.editor.view.dom
          range.setStart(div, div.childElementCount)
          range.setEnd(div, div.childElementCount)
        } else {
          range.setStart(this.lastSelection.startContainer, this.lastSelection.startOffset)
          range.setEnd(this.lastSelection.endContainer, this.lastSelection.endOffset)
        }
      }
    },
    onBlur (e) {
      this.$emit('blur', e)

      const sel = document.getSelection()
      if (sel.rangeCount === 1) {
        const range = sel.getRangeAt(0)
        this.lastSelection = {
          startContainer: range.startContainer,
          startOffset: range.startOffset,
          endContainer: range.endContainer,
          endOffset: range.endOffset
        }
      }
    },
    getContent () {
      if (this.withExtensions) {
        return [
          this.genToolbar(),
          this.genEditorContent()
        ]
      } else {
        return [
          this.genEditorContent()
        ]
      }
    },
    genToolbar () {
      return this.$createElement(EditorMenuBubble, {
        props: {
          editor: this.editor
        },
        scopedSlots: {
          default: (props) => {
            return this.$createElement('div', {
              staticClass: 'menububble',
              class: { 'is-active': props.menu.isActive },
              style: {
                left: props.menu.left + 'px',
                bottom: props.menu.bottom + 'px'
              }
            }, [
              this.$createElement(VToolbar, {
                props: { short: true }
              }, [
                this.$createElement(VItemGroup, {
                  staticClass: 'v-btn-toggle v-btn-toggle--dense',
                  props: { dense: true }
                }, [
                  this.genToolbarItem(props.isActive.heading({ level: 1 }), () => props.commands.heading({ level: 1 }), 'mdi-format-header-1'),
                  this.genToolbarItem(props.isActive.heading({ level: 2 }), () => props.commands.heading({ level: 2 }), 'mdi-format-header-2'),
                  this.genToolbarItem(props.isActive.heading({ level: 3 }), () => props.commands.heading({ level: 3 }), 'mdi-format-header-3')
                ]),
                this.$createElement('div', { staticClass: 'mx-1' }),
                this.$createElement(VItemGroup, {
                  staticClass: 'v-btn-toggle v-btn-toggle--dense',
                  props: { dense: true, multiple: true }
                }, [
                  this.genToolbarItem(props.isActive.bold(), props.commands.bold, 'mdi-format-bold'),
                  this.genToolbarItem(props.isActive.italic(), props.commands.italic, 'mdi-format-italic'),
                  this.genToolbarItem(props.isActive.underline(), props.commands.underline, 'mdi-format-underline'),
                  this.genToolbarItem(props.isActive.strike(), props.commands.strike, 'mdi-format-strikethrough')
                ])
              ])
            ])
          }
        }
      })
    },
    genToolbarItem (isActive, onClick, icon) {
      return this.$createElement(VBtn, {
        class: {
          'v-item--active': isActive,
          'v-btn--active': isActive
        },
        on: { click: onClick }
      }, [
        this.$createElement(VIcon, {}, [icon])
      ])
    },
    genEditorContent () {
      // TODO: Emit MouseDown/Up events
      return this.$createElement(EditorContent, {
        staticClass: 'editor__content',
        props: {
          editor: this.editor
        }
      })
    }
  },
  render (h) {
    return h('div', {
      staticClass: 'editor'
    }, this.getContent())
  }
}
</script>

<style scoped>

div.editor >>> p.is-editor-empty:first-child::before {
  content: attr(data-empty-text);
  float: left;
  color: #8B8B8B;
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
  line-height: normal !important;
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

div.editor >>> .menububble {
  position: absolute;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  z-index: 20;
  /*background: #000;*/
  border-radius: 5px;
  padding: .3rem;
  margin-bottom: .5rem;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
  visibility: hidden;
  opacity: 0;
  -webkit-transition: opacity .2s,visibility .2s;
  transition: opacity .2s,visibility .2s;
}

div.editor >>> .menububble.is-active {
  opacity: 1;
  visibility: visible;
}

div.editor >>> .menububble__button {
  border-radius: 3px;
}

div.editor >>> .menububble__button.is-active {
  background-color: hsla(0, 0%, 100%, .3);
}

</style>
