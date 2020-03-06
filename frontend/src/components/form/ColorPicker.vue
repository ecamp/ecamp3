<!--
Displays a field as a color picker (can be used with v-model)
-->

<template>
  <div>
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
            <v-icon :color="localValue" @click="on.click">
              mdi-palette
            </v-icon>
          </template>

          <!-- passing the append slot through -->
          <template v-slot:append>
            <slot name="append" />
          </template>
        </v-text-field>
      </template>
      <v-card>
        <v-color-picker v-if="showPicker" v-model="localValue" flat />
        <v-spacer />
        <v-btn text color="primary" @click="close">Cancel</v-btn>
        <v-btn text color="primary" @click="save">OK</v-btn>
      </v-card>
    </v-menu>
  </div>
</template>

<script>
import BasePicker from './BasePicker'

export default {
  name: 'ColorPicker',
  extends: BasePicker
}
</script>

<style scoped>
</style>
