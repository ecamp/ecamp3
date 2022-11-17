<!--
Displays a field as a picker (can be used with v-model)
-->

<template>
  <div ref="picker" class="e-form-container">
    <v-menu
      v-model="showPicker"
      :disabled="disabled || readonly"
      :close-on-click="false"
      :close-on-content-click="false"
      transition="scale-transition"
      offset-y
      offset-overflow
      min-width="290px"
      max-width="290px"
    >
      <template #activator="{ on }">
        <e-text-field
          ref="textField"
          :value="fieldValue"
          v-bind="$attrs"
          :error-messages="combinedErrorMessages"
          :filled="filled"
          :disabled="disabled"
          @click="on.click"
          @input="debouncedParseValue"
        >
          <template v-if="icon" #prepend>
            <v-icon :color="iconColor" @click="on.click">
              {{ icon }}
            </v-icon>
          </template>

          <!-- passing the append slot through -->
          <template #append>
            <slot name="append" />
          </template>
        </e-text-field>
      </template>
      <div :id="menuId">
        <slot :value="pickerValue" :on-input="inputFromPicker" :close="closePicker" />
      </div>
    </v-menu>
  </div>
</template>

<script>
import { debounce } from 'lodash'

export default {
  name: 'BasePicker',
  inheritAttr: false,
  props: {
    value: { type: [Number, String], required: true },
    icon: { type: String, required: false, default: null },
    iconColor: { type: String, required: false, default: null },
    readonly: { type: Boolean, required: false, default: false },
    disabled: { type: Boolean, required: false, default: false },
    filled: { type: Boolean, required: false, default: true },
    errorMessages: { type: Array, required: false, default: () => [] },
    closeOnPickerInput: { type: Boolean, required: false, default: false },

    /**
     * Format internal value for display in the UI
     */
    format: { type: Function, required: false, default: null },

    /**
     * Format internal value for the popup component. If omitted, uses format instead.
     */
    formatPicker: { type: Function, required: false, default: null },

    /**
     * Parse a user-supplied value into the internal format
     */
    parse: { type: Function, required: false, default: null },

    /**
     * Parse the value from the popup component into the internal format. If omitted, uses parse instead.
     */
    parsePicker: { type: Function, required: false, default: null },
  },
  data() {
    return {
      // internal random string used for identifying the menu in the DOM
      random: Math.random().toString(36).substring(2),

      // internal value
      localValue: null,
      localValueInitialized: false,

      showPicker: false,
      parseError: null,
      // note that it is necessary to debounce in data to have one debounced function per instance, whereas
      // debouncing in watch or methods results in one global debounced function which has unwanted effects
      // when there are multiple picker instances rendered at the same time
      debouncedParseValue: debounce(this.parseValue, 500),
      clickOutsideHandler: null,
      escapeKeyHandler: null,
    }
  },
  computed: {
    // value formatted for text field
    fieldValue() {
      if (this.format !== null) {
        return this.format(this.localValue)
      } else {
        return this.localValue
      }
    },

    // value formatted for picker component
    pickerValue() {
      if (this.formatPicker !== null) {
        return this.formatPicker(this.localValue)
      } else if (this.format !== null) {
        return this.format(this.localValue)
      } else {
        return this.localValue
      }
    },
    combinedErrorMessages() {
      if (this.parseError == null) {
        return this.errorMessages
      }
      return [...this.errorMessages, this.parseError.message]
    },
    menuId() {
      return 'picker-menu-' + this.random
    },
  },
  watch: {
    value(val) {
      this.localValueInitialized = false
      this.setValue(val)
    },
  },
  mounted() {
    const el = this.$refs.picker
    this.clickOutsideHandler = (event) => {
      const menuEl = document.querySelector(`#${this.menuId}`)
      if (
        !(
          el === event.target ||
          el.contains(event.target) ||
          menuEl?.contains(event.target)
        )
      ) {
        this.closePicker()
      }
    }
    document.addEventListener('click', this.clickOutsideHandler)

    this.escapeKeyHandler = (event) => {
      if (event.keyCode === 27) {
        this.closePicker()
      }
    }
    document.addEventListener('keydown', this.escapeKeyHandler)

    this.setValue(this.value)
  },
  beforeDestroy() {
    if (this.clickOutsideHandler) {
      document.removeEventListener('click', this.clickOutsideHandler)
    }
    if (this.escapeKeyHandler) {
      document.removeEventListener('keydown', this.escapeKeyHandler)
    }
  },
  methods: {
    setValue(val) {
      if (this.localValue !== val) {
        this.$emit('input', val)
        this.localValue = val

        if (this.localValueInitialized) {
          this.$emit('input', val)
          // after saving value, trigger validations
          this.$refs.textField.$refs.validationProvider.validate(this.fieldValue)
        }
      }
      this.localValueInitialized = true
      this.setParseError(null)
    },
    async parseValue(val) {
      try {
        if (this.parse != null) {
          val = await this.parse(val)
        }
        this.setValue(val)
      } catch (error) {
        this.setParseError(error)
      }
    },
    setParseError(err) {
      this.parseError = err
    },
    closePicker() {
      this.showPicker = false
    },
    async inputFromPicker(val) {
      try {
        if (this.parsePicker !== null) {
          val = await this.parsePicker(val)
        } else if (this.parse !== null) {
          val = await this.parse(val)
        }
        this.setValue(val)
        if (this.closeOnPickerInput) {
          this.closePicker()
        }
      } catch (error) {
        this.setParseError(error)
      }
    },
  },
}
</script>
