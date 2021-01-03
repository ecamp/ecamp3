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
          v-model="stringValue"
          v-bind="$attrs"
          :error-messages="combinedErrorMessages"
          :filled="filled"
          :disabled="disabled"
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
      localValue: this.value,
      localPickerValue: this.value,
      stringValue: '',
      showPicker: false,
      textFieldIsActive: false,
      parseError: null,
      eventHandlers: {
        save: this.savePicker,
        close: this.closePicker,
        input: this.inputPicker
      }
    }
  },
  computed: {
    fieldValue () {
      if (this.format !== null) {
        return this.format(this.localValue)
      } else {
        return this.localValue
      }
    },
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
    stringValue: debounce(function (val) {
      if (this.parse != null) {
        this.parse(val).then(this.setValue, this.setParseError)
      } else {
        this.setValue(val)
      }
    }, 500),
    value (val) {
      if (this.showPicker === false) {
        this.localValue = val
      }
    },
    fieldValue (val) {
      if (this.textFieldIsActive === false) {
        this.stringValue = val
      }
    },
    textFieldIsActive (val) {
      if (val === false) {
        this.stringValue = this.fieldValue
      }
    }
  },
  mounted () {
    this.stringValue = this.fieldValue
  },
  methods: {
    setValue (val) {
      if (this.localValue !== val) {
        this.$emit('input', val)
        this.localValue = val
      }
      this.parseError = null
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
