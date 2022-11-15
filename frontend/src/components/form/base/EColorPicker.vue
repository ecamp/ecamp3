<!--
Displays a field as a color picker (can be used with v-model)
-->
<template>
  <base-picker
    icon="mdi-palette"
    :icon-color="value"
    :value="value"
    v-bind="$attrs"
    :vee-id="veeId"
    :parse-picker="parsePicker"
    :vee-rules="veeRules || { regex: /#([a-f0-9]{3}){1,2}\b/i }"
    :required="required"
    @input="$emit('input', $event)"
  >
    <template #default="picker">
      <v-card>
        <v-color-picker :value="removeAlpha(picker.value)" flat @input="picker.onInput" />
        <v-btn text color="primary" @click="picker.close">
          {{ $tc('global.button.close') }}
        </v-btn>
      </v-card>
    </template>

    <!-- passing the append slot through -->
    <template #append>
      <slot name="append" />
    </template>
  </base-picker>
</template>

<script>
import BasePicker from './BasePicker.vue'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'

export default {
  name: 'EColorPicker',
  components: { BasePicker },
  mixins: [formComponentMixin],
  props: {
    value: { type: String, required: true },
  },
  methods: {
    parsePicker(val) {
      const result = this.removeAlpha(val)
      if (result.toLowerCase() === this.value.toLowerCase()) {
        // Avoid changing the case if that is all that has changed
        return Promise.resolve(this.value)
      }
      return Promise.resolve(result)
    },
    removeAlpha(hex) {
      return hex.length === 9 ? hex.substring(0, 7) : hex
    },
    update(picker, value) {
      picker.on.input(value)
    },
  },
}
</script>

<style scoped></style>
