<!--
Displays a field as a color picker (can be used with v-model)
-->
<template>
  <base-picker
    icon="mdi-palette"
    :icon-color="value"
    :value="value"
    v-bind="$attrs"
    @input="$emit('input', $event)">
    <template slot-scope="picker">
      <v-card>
        <v-color-picker v-if="picker.showPicker"
                        :value="picker.localValue"
                        flat
                        @input="picker.on.input" />
        <v-spacer />
        <v-btn text color="primary" @click="picker.on.close">{{ $tc('global.button.cancel') }}</v-btn>
        <v-btn text color="primary" @click="picker.on.save">{{ $tc('global.button.ok') }}</v-btn>
      </v-card>
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
  name: 'ColorPicker',
  components: { BasePicker },
  props: {
    value: { type: String, required: true }
  }
}
</script>

<style scoped>
</style>
