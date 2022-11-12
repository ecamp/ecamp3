<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-delete"
    :title="$tc('components.activity.content.storyboardDialogRemoveSection.title')"
    :submit-action="submit"
    submit-label="global.button.delete"
    submit-color="error"
    submit-icon="mdi-delete"
    cancel-icon=""
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <slot>{{
      $tc('components.activity.content.storyboardDialogRemoveSection.deleteWarning')
    }}</slot>
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'

export default {
  name: 'StoryboardDialogRemoveSection',
  components: { DialogForm },
  data() {
    return {
      showDialog: false,
    }
  },
  methods: {
    submit() {
      this.$emit('submit')
      this.close()
    },
    close() {
      this.showDialog = false
    },
    open() {
      this.showDialog = true
    },
  },
}
</script>
