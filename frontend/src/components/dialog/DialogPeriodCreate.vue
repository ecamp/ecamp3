<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-plus"
    title="Create Period"
    max-width="600px"
    :submit-action="createPeriod"
    submit-color="success"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <e-text-field
      v-model="entityData.description" hide-details="auto"
      label="Description"
      required />
    <e-text-field
      v-model="entityData.start" hide-details="auto"
      label="Start"
      required />
    <e-text-field
      v-model="entityData.end" hide-details="auto"
      label="End"
      required />
  </dialog-form>
</template>

<script>
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'

export default {
  name: 'DialogPeriodCreate',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'campId',
        'description',
        'start',
        'end'
      ],
      entityUri: '/period'
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({ campId: this.camp.id })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    }
  },
  methods: {
    createPeriod () {
      return this.create().then(() => {
        this.api.reload(this.camp)
      })
    }
  }
}
</script>

<style scoped>

</style>
