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
  History
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
    }
  },
  data () {
    return {
      emitAfterOnUpdate: false,
      editor: new Editor({
        extensions: [
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
        ],
        content: this.value,
        onUpdate: this.onUpdate,
        onFocus: this.onFocus,
        onBlur: this.onBlur
      }),
      regex: {
        emptyParagraph: new RegExp('<p></p>'),
        lineBreak1: new RegExp('<br>', 'g'),
        lineBreak2: new RegExp('<br/>', 'g')
      }
    }
  },
  watch: {
    value (val) {
      if (this.emitAfterOnUpdate) {
        this.emitAfterOnUpdate = false
        return
      }
      this.editor.setContent(val)
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
    },
    onBlur (e) {
      this.$emit('blur', e)
    },
    getContent () {
      return [
        this.genToolbar(),
        this.genEditorContent()
      ]
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

.v-text-field--filled:not(.v-text-field--single-line) div.editor {
  margin-top: 10px;
}

div.editor {
  -webkit-box-flex: 1;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
  padding: 8px 0 8px;
  max-width: 100%;
  min-width: 0;
  width: 100%;
}

div.editor >>> .editor__content {
  -webkit-box-flex: 1;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
  padding: 10px 0 0 0;
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
