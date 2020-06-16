<template>
  <ValidationProvider
    v-slot="{ errors: veeErrors }"
    :name="name"
    :vid="veeId"
    :rules="veeRules">
    <v-input
      v-bind="$attrs"
      :filled="filled"
      :error-messages="veeErrors.concat(errorMessages)"
      v-on="$listeners">
    <div class="editor" style="flex: 1 1 auto; line-height: normal">
        <editor-menu-bar v-slot="{ commands, isActive }" :editor="editor">
          <div class="menubar mb-2">
            <v-btn
              class="menubar__button mx-1 pa-1"
              :class="{ 'is-active': isActive.bold() }"
              @click="commands.bold">
              <v-icon>mdi-format-bold</v-icon>
            </v-btn>
            <v-btn
              class="menubar__button mx-1 pa-1"
              :class="{ 'is-active': isActive.italic() }"
              @click="commands.italic">
              <v-icon>mdi-format-italic</v-icon>
            </v-btn>
            <v-btn
              class="menubar__button mx-1 pa-1"
              :class="{ 'is-active': isActive.strike() }"
              @click="commands.strike">
              <v-icon>mdi-format-strikethrough</v-icon>
            </v-btn>
            <v-btn
              class="menubar__button mx-1 pa-1"
              :class="{ 'is-active': isActive.underline() }"
              @click="commands.underline">
              <v-icon>mdi-format-underline</v-icon>
            </v-btn>
            <v-btn
              class="menubar__button mx-1 pa-1"
              :class="{ 'is-active': isActive.paragraph() }"
              @click="commands.paragraph">
              <v-icon>mdi-format-paragraph</v-icon>
            </v-btn>
            <v-btn
              class="menubar__button mx-1 pa-1"
              :class="{ 'is-active': isActive.heading({ level: 1 }) }"
              @click="commands.heading({ level: 1 })">
              <v-icon>mdi-format-header-1</v-icon>
            </v-btn>
            <v-btn
              class="menubar__button mx-1 pa-1"
              :class="{ 'is-active': isActive.heading({ level: 2 }) }"
              @click="commands.heading({ level: 2 })">
              <v-icon>mdi-format-header-2</v-icon>
            </v-btn>
            <v-btn
              class="menubar__button mx-1 pa-1"
              :class="{ 'is-active': isActive.heading({ level: 3 }) }"
              @click="commands.heading({ level: 3 })">
              <v-icon>mdi-format-header-3</v-icon>
            </v-btn>
            <v-btn
              class="menubar__button mx-1 pa-1"
              :class="{ 'is-active': isActive.bullet_list() }"
              @click="commands.bullet_list">
              <v-icon>mdi-format-list-bulleted</v-icon>
            </v-btn>
            <v-btn
              class="menubar__button mx-1 pa-1"
              :class="{ 'is-active': isActive.ordered_list() }"
              @click="commands.ordered_list">
              <v-icon>mdi-format-list-numbered</v-icon>
            </v-btn>
            <v-btn
              class="menubar__button mx-1 pa-1"
              @click="commands.undo">
              <v-icon>mdi-undo</v-icon>
            </v-btn>
            <v-btn
              class="menubar__button mx-1 pa-1"
              @click="commands.redo">
              <v-icon>mdi-redo</v-icon>
            </v-btn>
          </div>
        </editor-menu-bar>
        <editor-content class="editor__content" :editor="editor" />
      </div>
    </v-input>
  </ValidationProvider>
</template>

<script>
import { Editor, EditorContent, EditorMenuBar } from 'tiptap'
import {
  Heading,
  OrderedList,
  BulletList,
  ListItem,
  TodoItem,
  TodoList,
  Bold,
  Italic,
  Strike,
  Underline,
  History,
  TrailingNode
} from 'tiptap-extensions'
import { ValidationProvider } from 'vee-validate'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin'

export default {
  name: 'ERichtext',
  components: {
    EditorContent,
    EditorMenuBar,
    ValidationProvider
  },
  mixins: [formComponentPropsMixin],
  props: {
    value: { type: String, default: '', required: true }
  },
  data () {
    return {
      editor: null,
      editorValue: null
    }
  },
  watch: {
    value (val) {
      if (this.editor && val !== this.editorValue) {
        this.editor.setContent(val, true)
      }
    }
  },
  mounted () {
    this.editor = new Editor({
      extensions: [
        new BulletList(),
        new Heading({ levels: [1, 2, 3] }),
        new ListItem(),
        new OrderedList(),
        new TodoItem(),
        new TodoList(),
        new Bold(),
        new Italic(),
        new Strike(),
        new Underline(),
        new History(),
        new TrailingNode({
          node: 'paragraph',
          notAfter: ['paragraph']
        })
      ],
      content: this.value,
      onUpdate: ({ getHTML }) => {
        this.editorValue = getHTML()
        this.$emit('input', this.editorValue)
      }
    })
    this.editor.setContent(this.value)
  },
  beforeDestroy () {
    if (this.editor) {
      this.editor.destroy()
    }
  }
}
</script>

<style scoped>
  div.menubar >>> button {
    min-width: 40px !important;
  }
  div.editor__content >>> p {
    margin: 0;
  }
  div.editor__content >>> div.ProseMirror {
    padding: 4px !important;
  }
</style>
