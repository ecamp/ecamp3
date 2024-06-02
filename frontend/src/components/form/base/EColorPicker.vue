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
    button-aria-label-i18n-key="components.form.base.eColorPicker.openPicker"
    open-on-text-field-click
    @input="$emit('input', $event)"
  >
    <template #prepend="{ color, attrs, on }">
      <ColorSwatch :color="color" class="mt-n1" v-bind="attrs" v-on="on" />
    </template>
    <template #default="picker">
      <v-card :style="{ '--picker-contrast-color': contrast }" data-testid="colorpicker">
        <v-color-picker
          :value="removeAlpha(picker.value || '')"
          flat
          @input="picker.onInput"
        />
        <v-divider />
        <div class="d-flex gap-2 pa-4 flex-wrap">
          <ColorSwatch
            v-for="swatch in swatches"
            :key="swatch"
            :color="swatch"
            @select-color="picker.onInput"
          />
        </div>
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
import { contrastColor } from '@/common/helpers/colors.js'
import ColorSwatch from '@/components/form/base/ColorPicker/ColorSwatch.vue'

export default {
  name: 'EColorPicker',
  components: { ColorSwatch, BasePicker },
  mixins: [formComponentMixin],
  props: {
    value: { type: String, required: true },
  },
  data: () => ({
    swatches: [
      '#90B7E4',
      '#6EDBE9',
      '#4dbb52',
      '#FF9800',
      '#FD7A7A',
      '#d584e9',
      '#BBBBBB',

      '#1964B1',
      '#1E86CA',
      '#3DB842',
      '#F1810D',
      '#C71A1A',
      '#CF3BD6',
      '#575757',
    ],
  }),
  computed: {
    contrast() {
      // Vuetify returns invalid value #NANNAN in the initialization phase
      return this.value && this.value !== '#NANNAN' ? contrastColor(this.value) : 'black'
    },
  },
  methods: {
    /**
     * Format internal value for display in the UI
     */
    format(val) {
      return val || ''
    },
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

<style scoped>
:deep(.v-color-picker__dot > div::before) {
  content: '•';
  color: var(--picker-contrast-color);
  display: block;
  width: 100%;
  line-height: 26px;
  font-size: 28px;
  text-align: center;
}
</style>
