<!--
Displays a field as a color picker (can be used with v-model)
-->
<template>
  <base-picker
    icon="mdi-palette"
    :icon-color="value"
    :value="value"
    v-bind="$attrs"
    :parse-picker="parsePicker"
    @input="$emit('input', $event)">
    <template slot-scope="picker">
      <v-card>
        <v-color-picker v-if="picker.showPicker"
                        :value="picker.value"
                        flat
                        @input="picker.on.input" />
        <v-spacer />
        <v-btn text color="primary" @click="picker.on.close">Cancel</v-btn>
        <v-btn text color="primary" @click="picker.on.save">OK</v-btn>
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
  name: 'EColorPicker',
  components: { BasePicker },
  props: {
    value: { type: String, required: true }
  },
  methods: {
    parsePicker (val) {
      if (typeof val === 'object') return Promise.resolve(val.hex)
      return Promise.resolve(val)
    }
  }
}
</script>

<style scoped>
</style>
