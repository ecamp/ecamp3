<template>
  <ValidationProvider
    v-slot="{ errors: veeErrors }"
    :name="name"
    :vid="veeId"
    :rules="veeRules">
    <tiptap-vuetify
      v-bind="$attrs"
      :extensions="extensions"
      :filled="filled"
      :hide-details="hideDetails"
      :error-messages="veeErrors.concat(errorMessages)"
      :label="label || name"
      :class="[my === false ? '' :'my-' + my, inputClass]"
      v-on="$listeners" />
  </ValidationProvider>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import {
  TiptapVuetify,
  Heading,
  Bold,
  Italic,
  Strike,
  Underline,
  Paragraph,
  BulletList,
  OrderedList,
  ListItem,
  Link,
  HardBreak,
  History
} from 'tiptap-vuetify'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin'

export default {
  name: 'ERichtext',
  components: {
    ValidationProvider,
    TiptapVuetify
  },
  mixins: [formComponentPropsMixin],
  data () {
    return {
      extensions: [
        History,
        Link,
        Underline,
        Strike,
        Italic,
        ListItem,
        BulletList,
        OrderedList,
        [Heading, {
          options: {
            levels: [1, 2, 3]
          }
        }],
        Bold,
        Paragraph,
        HardBreak
      ]
    }
  }
}
</script>

<style scoped>
  div.tiptap-vuetify-editor >>> div.tiptap-vuetify-editor__content p {
    margin: 0;
  }
  div.tiptap-vuetify-editor >>> div.tiptap-vuetify-editor__content h1,
  div.tiptap-vuetify-editor >>> div.tiptap-vuetify-editor__content h2,
  div.tiptap-vuetify-editor >>> div.tiptap-vuetify-editor__content h3 {
    line-height: normal;
    margin: 0.5em 0 0 0;
  }
  div.tiptap-vuetify-editor >>> div.tiptap-vuetify-editor__content div.ProseMirror {
    padding: 4px !important;
  }
  div.tiptap-vuetify-editor >>> div.tiptap-vuetify-editor__content div.ProseMirror :first-child {
    margin: 0;
  }
</style>
