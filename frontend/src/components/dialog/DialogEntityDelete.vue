<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-delete"
    :title="$t('title')"
    max-width="600px"
    :submit-action="del"
    :submit-label="$t('title')"
    submit-color="error"
    submit-icon="mdi-delete"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <slot>{{ $t('warningText') }}</slot>?
  </dialog-form>
</template>

<i18n>
{
  "en": {
    "title": "Delete",
    "warningText": "Do you really want to delete this?"
  },
  "de": {
    "title": "Löschen",
    "warningText": "Willst du diesen Inhalt wirklich löschen?"
  }
}
</i18n>

<script>
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'

export default {
  name: 'DialogEntityDelete',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    entity: { type: Object, required: true }
  },
  created () {
    this.entityUri = this.entity._meta.self
  }
}
</script>

<style scoped>

</style>
