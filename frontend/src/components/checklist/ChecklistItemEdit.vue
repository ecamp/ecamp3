<template>
  <DetailPane
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-account-plus"
    :title="$tc('components.checklist.checklistItemEdit.title')"
    :submit-action="update"
    :submit-label="$tc('global.button.submit')"
    submit-icon="mdi-send"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="{ on }">
      <slot name="activator" v-bind="{ on }" />
    </template>

    <e-text-field
      v-model="entityData.text"
      type="text"
      path="text"
      vee-rules="required"
      autofocus
    />
  </DetailPane>
</template>

<script>
import DetailPane from '@/components/generic/DetailPane.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'

export default {
  name: 'ChecklistItemEdit',
  components: { DetailPane },
  extends: DialogBase,
  provide() {
    return {
      entityName: 'checklistItem',
    }
  },
  props: {
    checklist: { type: Object, required: true },
    checklistItem: { type: Object, default: null },
  },
  data() {
    return {
      entityProperties: ['checklist', 'text'],
      entityUri: '',
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.entityUri = this.checklistItem._meta.self
        this.setEntityData({
          text: this.checklistItem.text,
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    },
  },
  mounted() {
    this.api.href(this.api.get(), 'checklistItems').then((uri) => (this.entityUri = uri))
  },
}
</script>

<style scoped></style>
