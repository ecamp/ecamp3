<!--
Displays a field as a date picker (can be used with v-model)
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
      <template v-slot:activator="{on}">
        <e-text-field
          v-model="stringValue"
          v-bind="$attrs"
          :error-messages="combinedErrorMessages"
          :filled="filled"
          :disabled="disabled"
          @focus="textFieldIsActive = true"
          @blur="textFieldIsActive = false">
          <template v-if="icon" v-slot:prepend>
            <v-icon :color="iconColor" @click="on.click">
              {{ icon }}
            </v-icon>
          </template>

          <!-- passing the append slot through -->
          <template v-slot:append>
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
    format: { type: Function, required: false, default: null },
    formatPicker: { type: Function, required: false, default: null },
    parse: { type: Function, required: false, default: null },
    parsePicker: { type: Function, required: false, default: null },
    errorMessages: { type: Array, required: false, default: () => [] }
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
      if (this.format != null) {
        return this.format(this.localValue)
      } else {
        return this.localValue
      }
    },
    pickerValue () {
      if (this.formatPicker != null) {
        return this.formatPicker(this.localValue)
      } else {
        return ''
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
    stringValue (val) {
      if (this.parse != null) {
        this.parse(val).then(this.setValue, this.setParseError)
      } else {
        this.setValue(val)
      }
    },
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
      if (this.parsePicker) {
        this.parsePicker(val).then(this.setValueOfPicker, this.setParseError)
      } else {
        this.setValueOfPicker(val)
      }
    }
  }
}
</script>
