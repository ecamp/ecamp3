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
    :vee-rules="{ regex: /#([a-f0-9]{3}){1,2}\b/i }"
    @input="$emit('input', $event)">
    <template slot-scope="picker">
      <v-card>
        <v-color-picker v-if="picker.showPicker"
                        :value="picker.value"
                        flat
                        @input="picker.on.input" />
        <v-spacer />
        <v-btn text color="primary" @click="picker.on.close" data-testid="action-cancel">{{ $tc('global.button.cancel') }}</v-btn>
        <v-btn text color="primary" @click="picker.on.save" data-testid="action-ok">{{ $tc('global.button.ok') }}</v-btn>
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
      if (typeof val === 'object') return Promise.resolve(this.removeAlpha(val.hex))
      return Promise.resolve(this.removeAlpha(val))
    },
    removeAlpha (hex) {
      return hex.length === 9 ? hex.substring(0, 7) : hex
    }
  }
}
</script>

<style scoped>
</style>
