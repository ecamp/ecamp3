<template>
  <dialog-form
    v-model="visible"
    icon="mdi-calendar-plus"
    title="Create Period"
    max-width="600px"
    :create="createPeriod"
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
      <input type="hidden" name="camp_id" :value="entityData.camp_id">
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
  watch: {
    value: function (camp) {
      if (camp != null) {
        this.entityUri = '/period'
        this.setEntityData({ camp_id: camp.id })
      } else {
        this.entityUri = ''
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
  },
  methods: {
    createPeriod () {
      const camp = this.value
      return this.create().then(() => {
        this.api.reload(camp)
      })
    }
  }
}
</script>

<style scoped>

</style>
