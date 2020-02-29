<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-edit"
    :title="entityData.description"
    max-width="600px"
    :submit-action="update"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <v-row>
      <v-col cols="12">
        <v-text-field
          v-model="entityData.description"
          label="Description"
          required />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="entityData.start"
          label="Start"
          required />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="entityData.end"
          label="End"
          required />
      </v-col>
    </v-row>
  </dialog-form>
</template>

<script>
import DialogBase from './DialogBase'
import DialogForm from './DialogForm'
export default {
  name: 'EditPeriodDialog',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    period: { type: Object, required: true }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.period._meta.self)
      }
    }
  },
  created () {
    this.entityProperties = [
      'description',
      'start',
      'end'
    ]
  }
}
</script>

<style scoped>

</style>
