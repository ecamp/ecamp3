<!--
Displays a field as a picker (can be used with v-model)
-->

<template>
  <div class="e-form-container">
    <v-menu
      ref="menu"
      v-model="showPicker"
      :disabled="disabled || readonly"
      :close-on-content-click="false"
      transition="scale-transition"
      offset-y
      offset-overflow
      min-width="290px"
      max-width="290px">
      <template #activator="{on}">
        <e-text-field
          :value="fieldValue"
          v-bind="$attrs"
          :error-messages="combinedErrorMessages"
          :filled="filled"
          :disabled="disabled"
          @input="debouncedParseValue"
          @focus="textFieldIsActive = true"
          @blur="textFieldIsActive = false">
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
      <slot :value="pickerValue"
            :showPicker="showPicker"
            :on="eventHandlers" />
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
    parsePicker: { type: Function, required: false, default: null }
  },
  data () {
    return {
      // internal value
      localValue: this.value,

      // value used to pass to picker component
      localPickerValue: this.value,

      showPicker: false,
      textFieldIsActive: false,
      parseError: null,
      eventHandlers: {
        save: this.savePicker,
        close: this.closePicker,
        input: this.inputPicker
      },
      // note that it is necessary to debounce in data to have one debounced function per instance, whereas
      // debouncing in watch or methods results in one global debounced function which has unwanted effects
      // when there are multiple picker instances rendered at the same time
      debouncedParseValue: debounce(this.parseValue, 500)
    }
  },
  computed: {

    // value formatted for text field
    fieldValue () {
      if (this.format !== null) {
        return this.format(this.localValue)
      } else {
        return this.localValue
      }
    },

    // value formatted for picker component
    pickerValue () {
      if (this.formatPicker !== null) {
        return this.formatPicker(this.localValue)
      } else if (this.format !== null) {
        return this.format(this.localValue)
      } else {
        return this.localValue
      }
    },
    combinedErrorMessages () {
      if (this.parseError == null) {
        return this.errorMessages
      }
      return [...this.errorMessages, this.parseError.message]
    }
  },
  watch: {
    value (val) {
      if (this.showPicker === false) {
        this.localValue = val
      }
    }
  },
  mounted () {
    this.parseValue(this.fieldValue)
  },
  methods: {
    setValue (val) {
      if (this.localValue !== val) {
        this.$emit('input', val)
        this.localValue = val
      }
      this.parseError = null
    },
    parseValue (val) {
      if (this.parse != null) {
        this.parse(val).then(this.setValue, this.setParseError)
      } else {
        this.setValue(val)
      }
    },
    setValueOfPicker (val) {
      if (this.localPickerValue !== val) {
        this.localPickerValue = val
      }
      this.parseError = null
    },
    setParseError (err) {
      this.parseError = err
    },
    closePicker () {
      this.showPicker = false
      this.localPickerValue = this.localValue
    },
    savePicker () {
      this.showPicker = false
      this.setValue(this.localPickerValue)
    },
    inputPicker (val) {
      if (this.parsePicker !== null) {
        this.parsePicker(val).then(this.setValueOfPicker, this.setParseError)
      } else if (this.parse !== null) {
        this.parse(val).then(this.setValueOfPicker, this.setParseError)
      } else {
        this.setValueOfPicker(val)
      }
    }
  }
}
</script>
