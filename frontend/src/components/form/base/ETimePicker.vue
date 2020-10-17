<!--
Displays a field as a time picker (can be used with v-model)
Allows 15min steps only
-->

<template>
  <base-picker
    :icon="icon"
    :value="value"
    v-bind="$attrs"
    @input="$emit('input', $event)">
    <template slot-scope="picker">
      <v-time-picker
        :value="picker.value || ''"
        :allowed-minutes="allowedStep"
        format="24hr"
        scrollable
        @input="picker.on.input">
        <v-spacer />
        <v-btn text color="primary" @click="picker.on.close">Cancel</v-btn>
        <v-btn text color="primary" @click="picker.on.save">OK</v-btn>
      </v-time-picker>
    </template>

    <!-- passing the append slot through -->
    <template v-slot:append>
      <slot name="append" />
    </template>
  </base-picker>
</template>

<script>
import BasePicker from './BasePicker'

export default {
  name: 'ETimePicker',
  components: { BasePicker },
  props: {
    icon: { type: String, required: false, default: 'mdi-clock-outline' },
    value: { type: String, required: true }
  },
  methods: {
    allowedStep: m => m % 15 === 0
  }
}
</script>

<style scoped>
</style>
