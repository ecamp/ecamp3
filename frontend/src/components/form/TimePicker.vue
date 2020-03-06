<!--
Displays a field as a time picker (can be used with v-model)
Allows 15min steps only
-->

<template>
  <v-menu
    ref="menu"
    v-model="showPicker"
    :close-on-content-click="false"
    transition="scale-transition"
    offset-y
    offset-overflow
    min-width="290px"
    max-width="290px">
    <template v-slot:activator="{on}">
      <v-text-field
        v-model="localValue"
        v-bind="$attrs"
        readonly
        hide-details="auto"
        outlined
        v-on="on">
        <template v-slot:prepend>
          <v-icon @click="on.click">
            mdi-clock-outline
          </v-icon>
        </template>

        <!-- passing the append slot through -->
        <template v-slot:append>
          <slot name="append" />
        </template>
      </v-text-field>
    </template>
    <v-time-picker
      v-model="localValue"
      :allowed-minutes="allowedStep"
      format="24hr"
      scrollable>
      <v-spacer />
      <v-btn text color="primary" @click="close">Cancel</v-btn>
      <v-btn text color="primary" @click="save">OK</v-btn>
    </v-time-picker>
  </v-menu>
</template>

<script>
import BasePicker from './BasePicker'

export default {
  name: 'TimePicker',
  extends: BasePicker,
  methods: {
    allowedStep: m => m % 15 === 0
  }
}
</script>

<style scoped>
</style>
