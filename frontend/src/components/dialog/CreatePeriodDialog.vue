<template>
  <dialog-form
    icon="mdi-calendar-plus"
    title="Create Period"
    max-width="600px"
    :submit-action="createPeriod"
    submit-color="success"
    :cancel-action="close"
    :value="value"
    v-bind="$attrs"
    @input="$emit('input', $event)">
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
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'
export default {
  name: 'CreatePeriodDialog',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true }
  },
  watch: {
    value: function (value) {
      if (value) {
        this.setEntityData({ camp_id: this.camp.id })
      } else {
      // clear form on exit
        this.clearEntityData()
      }
    }
  },
  created () {
    this.entityProperties = [
      'camp_id',
      'description',
      'start',
      'end'
    ]
    this.entityUri = '/period'
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
