<template>
  <div class="e-form-container">
    <v-card v-for="scheduleEntry in scheduleEntries"
            :key="scheduleEntry.id"
            outlined
            color="grey lighten-3" class="period mb-2 rounded-b-0">
      <v-row no-gutters>
        <v-col>
          <legend class="pa-2">
            {{ $tc('entity.scheduleEntry.name') }}
          </legend>
        </v-col>
      </v-row>
      <v-row no-gutters class="mx-2 mb-2">
        <v-col>
          <e-text-field
            v-model="scheduleEntry.periodOffset"
            :name="$tc('entity.scheduleEntry.fields.periodOffset')"
            vee-rules="required"
            :my="2"
            :filled="false"
            required />
        </v-col>
        <v-col>
          <e-text-field
            v-model="scheduleEntry.length"
            input-class="ml-2"
            :name="$tc('entity.scheduleEntry.fields.length')"
            vee-rules="required"
            :my="2"
            :filled="false"
            icon=""
            required />
        </v-col>
      </v-row>
    </v-card>
  </div>
</template>
<script>

export default {
  name: 'CreateActivityScheduleEntries',
  props: {
    scheduleEntries: {
      type: Array,
      required: true
    }
  }
}
</script>
<style scoped lang="scss">
  .period.period {
    border-bottom-width: 1px !important;
    border-bottom-style: solid !important;
    border-bottom-color: rgba(0, 0, 0, 0.42) !important;
  }
</style>
