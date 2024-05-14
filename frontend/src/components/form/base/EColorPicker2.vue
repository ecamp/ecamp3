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
      :close-on-click="false"
      :close-on-content-click="false"
      min-width="290px"
      max-width="290px"
      @input="onPickerClose"
    >
      <template #activator="{ on }">
        <div v-on="on">
          <EColorField
            ref="input"
            :value="pickerValue"
            :vee-id="veeId"
            :vee-rules="veeRules"
            v-bind="$attrs"
            @input="onInput($event)"
            @click.prevent="onInputClick"
          >
            <template #prepend="{ serializedValue }">
              <ColorSwatch
                ref="inputSwatch"
                :color="serializedValue"
                class="mt-n1"
                :aria-label="
                  pickerOpen ? $tc('global.button.close') : $tc('global.button.open')
                "
                aria-haspopup="true"
                :aria-expanded="pickerOpen ? 'true' : 'false'"
                @click="onInputSwatchClick"
              />
            </template>
          </EColorField>
        </div>
      </template>
      <v-card ref="picker" :ripple="false" tabindex="-1">
        <v-color-picker
          :value="pickerValue"
          :style="{ '--picker-contrast-color': contrast }"
          flat
          @update:color="debouncedPickerValue($event.hex)"
        />
        <v-divider />
        <div class="d-flex gap-2 pa-4 flex-wrap">
          <ColorSwatch
            v-for="swatch in swatches"
            :key="swatch"
            :color="swatch"
            @selectColor="onSwatchSelect($event)"
          />
        </div>
      </v-card>
    </v-menu>
  </div>
</template>

<script>
import { formComponentMixin } from '@/mixins/formComponentMixin.js'
import { contrastColor } from '@/common/helpers/colors.js'
import ColorSwatch from '@/components/form/base/ColorPicker/ColorSwatch.vue'
import { debounce } from 'lodash'

export default {
  name: 'EColorPicker2',
  components: { ColorSwatch },
  mixins: [formComponentMixin],
  inheritAttrs: false,
  props: {
    value: { type: String, required: true },
  },
  emits: ['input'],
  data: () => ({
    pickerOpen: false,
    pickerValue: null,
    debouncedPickerValue: null,
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
  watch: {
    value: {
      handler(newValue) {
        this.pickerValue = newValue
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
    this.debouncedPickerValue = debounce(this.onInput, 300)
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
      this.$emit('input', this.pickerValue)
    },
    onSwatchSelect(color) {
      this.pickerValue = color
      this.$emit('input', this.pickerValue)
      this.pickerOpen = false
      this.$refs.inputSwatch.$el.focus()
    },
    closePicker() {
      this.pickerOpen = false
    },
    closePickerConditional(event) {
      return this.pickerOpen && !this.$refs.picker?.$el.contains(event.target)
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
