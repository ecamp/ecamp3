<template>
  <dialog-form
    v-model="visible"
    icon="mdi-calendar-edit"
    :title="entityData.description"
    max-width="600px"
    :update="update"
    :cancel="cancel">
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
  watch: {
    value: function (uri) {
      this.loadEntityData(uri)
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
