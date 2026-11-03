<!--
Displays a field as a color picker (can be used with v-model)
-->
<template>
  <div
    v-click-outside="{ handler: closePicker, closeConditional: closePickerConditional }"
    class="e-form-container"
  >
    <v-menu
      v-model="pickerOpen"
      transition="scale-transition"
      offset-y
      offset-overflow
      :open-on-click="false"
      persistent
      :close-on-content-click="false"
      min-width="290px"
      max-width="290px"
      @update:model-value="onPickerClose"
    >
      <template #activator="{ props }">
        <div v-bind="props">
          <EColorField
            v-bind="$attrs"
            :id="id"
            ref="input"
            :model-value="pickerValue"
            :vee-id="veeId"
            :vee-rules="veeRules"
            :hide-details="hideDetails"
            :input-class="inputClass"
            :required="required"
            :path="path"
            :label="label"
            :validation-label-override="validationLabelOverride"
            :error-messages="errorMessages"
            @blur="onBlur"
            @update:model-value="onInput($event)"
            @click.prevent="onInputClick"
          >
            <template #prepend="{ serializedValue }">
              <ColorSwatch
                ref="inputSwatch"
                :color="serializedValue"
                class="mt-n1"
                :aria-label="
                  pickerOpen
                    ? $t('components.form.base.eColorPicker.closePicker')
                    : $t(
                        'components.form.base.eColorPicker.openPicker',
                        { label: labelOrEntityFieldLabel },
                        0
                      )
                "
                aria-haspopup="true"
                :aria-expanded="pickerOpen ? 'true' : 'false'"
                @click="onInputSwatchClick"
              />
            </template>
            <template #append-inner>
              <slot name="append-inner" />
            </template>
          </EColorField>
        </div>
      </template>
      <v-card
        ref="picker"
        :ripple="false"
        tabindex="-1"
        data-testid="colorpicker"
        @input="onPickerFieldInput"
      >
        <v-color-picker
          v-if="pickerNull"
          key="model"
          model-value="#FF0000"
          :modes="['hex', 'rgb', 'hsl']"
          class="w-100"
          elevation="0"
          :style="{ '--picker-contrast-color': contrast }"
          flat
          @update:model-value="onPickerInput($event)"
        />
        <v-color-picker
          v-else
          key="null"
          :model-value="pickerValue"
          :modes="['hex', 'rgb', 'hsl']"
          class="w-100"
          elevation="0"
          :style="{ '--picker-contrast-color': contrast }"
          flat
          @update:model-value="onPickerInput($event)"
        />
        <v-divider />
        <div class="d-flex gap-2 pa-4 flex-wrap">
          <ColorSwatch
            v-for="swatch in swatchesWithReset"
            :key="swatch"
            :color="swatch"
            @select-color="onSwatchSelect($event)"
          />
          <ColorSwatch
            v-if="!required"
            color="#000000"
            class="reset"
            @select-color="onSwatchSelect(null)"
          />
        </div>
      </v-card>
    </v-menu>
  </div>
</template>

<script>
import { formComponentValidation } from '@/mixins/formComponentValidation.js'
import { contrastColor } from '@/helpers/colors.js'
import ColorSwatch from '@/components/form/base/ColorPicker/ColorSwatch.vue'
import { debounce } from 'lodash-es'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'

const DEBOUNCE_MS = 500
const HEX3_PATTERN = /^#([0-9A-Fa-f])([0-9A-Fa-f])([0-9A-Fa-f])$/
const HEX6_PATTERN = /^#[0-9A-Fa-f]{6}$/

export default {
  name: 'EColorPicker',
  components: { ColorSwatch },
  mixins: [formComponentValidation, formComponentPropsMixin],
  inheritAttrs: false,
  props: {
    modelValue: { type: String, required: false, default: null },
  },
  emits: ['update:modelValue', 'blur'],
  data: () => ({
    pickerOpen: false,
    pickerValue: null,
    pickerNull: false,
    debouncedEmit: null,
    debouncedPickerUpdate: null,
    swatches: [
      '#90B7E4',
      '#6EDBE9',
      '#4DBB52',
      '#FF9800',
      '#FD7A7A',
      '#D584E9',
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
      try {
        // Vuetify returns invalid value #NANNAN in the initialization phase
        return this.modelValue && this.modelValue !== '#NANNAN'
          ? contrastColor(this.modelValue)
          : 'black'
      } catch {
        return 'black'
      }
    },
    swatchesWithReset() {
      return this.required
        ? this.swatches
        : this.swatches.slice(0, this.swatches.length - 1)
    },
  },
  watch: {
    modelValue: {
      handler(newValue) {
        this.pickerValue = newValue
        this.pickerNull = [null, ''].includes(newValue)
      },
      immediate: true,
    },
    pickerOpen: {
      handler(open) {
        if (!open) {
          this.onPickerClose()
        }
      },
    },
  },
  created() {
    this.debouncedEmit = debounce((value) => {
      this.$emit('update:modelValue', value)
    }, DEBOUNCE_MS)
    this.debouncedPickerUpdate = debounce((value) => {
      this.pickerValue = value
      this.pickerNull = false
      this.$emit('update:modelValue', value)
    }, DEBOUNCE_MS)
  },
  mounted() {
    document.addEventListener('keydown', this.escapeHandler)
  },
  beforeUnmount() {
    document.removeEventListener('keydown', this.escapeHandler)
  },
  methods: {
    onInputClick() {
      this.activator = 'input'
      this.pickerOpen = true
    },
    onInputSwatchClick() {
      this.activator = 'swatch'
      this.pickerOpen = !this.pickerOpen
      if (this.pickerOpen) {
        setTimeout(() => {
          this.$refs.picker?.$el.focus()
        }, 100)
      }
    },
    onInput(value) {
      this.pickerValue = value
      this.$emit('update:modelValue', this.pickerValue)
    },
    onBlur(event) {
      if (!this.pickerOpen) {
        this.$emit('blur', event)
      }
    },
    onPickerInput(value) {
      this.pickerValue = value.toUpperCase()
      this.pickerNull = false
      this.debouncedEmit(this.pickerValue)
    },
    onSwatchSelect(color) {
      this.pickerValue = color
      this.$emit('update:modelValue', this.pickerValue)
      this.pickerOpen = false
      this.$refs.inputSwatch.$el.focus()
    },
    closePicker() {
      this.pickerOpen = false
    },
    closePickerConditional(event) {
      return this.pickerOpen && !this.$refs.picker?.$el.contains(event.target)
    },
    onPickerFieldInput(e) {
      /**
       * Validates and normalizes input values in the color picker's edit fields.
       * For number inputs (RGB/HSL values), clamps them within their min/max bounds.
       * For text inputs (hex values), expands 3-digit hex codes to 6-digit format
       * and validates proper hex color syntax.
       * - in basic v-color-picker you can force an alpha channel if you enter e.g. 400 in rgb, clamping
       *   below avoids that
       * - basic v-color-picker does not update the modelValue after typing (only when focusing away)
       *   so we call debouncedPickerUpdate to update the color preview and the modelValue after a short delay
       */
      if (e.target.tagName !== 'INPUT') return
      if (e.target.type === 'number') {
        const raw = parseFloat(e.target.value)
        if (!isNaN(raw)) {
          const min = e.target.min !== '' ? parseFloat(e.target.min) : 0
          const max = e.target.max !== '' ? parseFloat(e.target.max) : 255
          e.target.value = String(Math.min(Math.max(raw, min), max))
        }
        e.target.dispatchEvent(new Event('change', { bubbles: true }))
      } else if (e.target.type === 'text') {
        const hex3 = e.target.value.match(HEX3_PATTERN)
        const hex6 = e.target.value.match(HEX6_PATTERN)
        if (hex3) {
          this.debouncedPickerUpdate?.(
            `#${hex3[1]}${hex3[1]}${hex3[2]}${hex3[2]}${hex3[3]}${hex3[3]}`.toUpperCase()
          )
        } else if (hex6) {
          this.debouncedPickerUpdate?.(e.target.value.toUpperCase())
        }
      }
    },
    onPickerClose() {
      switch (this.activator) {
        case 'input':
          this.$refs.input.focus()
          break
        case 'swatch':
          this.$refs.inputSwatch.$el.focus()
          break
      }
      this.$emit('blur')
    },
    escapeHandler(event) {
      switch (event.code) {
        case 'Escape':
          this.closePicker()
          break
      }
    },
  },
}
</script>

<style scoped>
:deep(.v-color-picker-edit__input input) {
  background: rgb(var(--v-theme-surface));
  border: 1px solid rgba(var(--v-theme-on-surface), 0.38);
}

:deep(.v-color-picker-preview__dot > div::before) {
  content: '•';
  color: var(--picker-contrast-color);
  display: block;
  width: 100%;
  line-height: 26px;
  font-size: 28px;
  text-align: center;
}

:deep(.e-colorswatch.reset::before) {
  content: '×';
}
</style>
