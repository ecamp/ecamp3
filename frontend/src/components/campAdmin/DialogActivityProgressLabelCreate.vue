<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-progress-check"
    :title="$tc('components.campAdmin.dialogActivityProgressLabelCreate.title')"
    :submit-action="createDialogActivityProgressLabelCreate"
    :submit-label="$tc('global.button.create')"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-activity-progress-label-form :activity-progress-label="entityData" />
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogActivityProgressLabelForm from './DialogActivityProgressLabelForm.vue'

export default {
  name: 'DialogActivityProgressLabelCreate',
  components: { DialogForm, DialogActivityProgressLabelForm },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['camp', 'position', 'title'],
      entityUri: '',
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        const labels = this.camp.progressLabels().items
        const maxPosition = Math.max(0, ...labels.map((l) => l.position))

        this.setEntityData({
          camp: this.camp._meta.self,
          position: maxPosition + 1,
          title: '',
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    },
  },
  mounted() {
    this.api
      .href(this.api.get(), 'activityProgressLabels')
      .then((uri) => (this.entityUri = uri))
  },
  methods: {
    createDialogActivityProgressLabelCreate() {
      return this.create().then(() => {
        this.api.reload(this.camp.progressLabels())
      })
    },
  },
}
</script>

<style scoped></style>
