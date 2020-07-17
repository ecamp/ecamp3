<!--
Displays a field as a date picker (can be used with v-model)
-->

<template>
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
</template>

<script>

export default {
  name: 'BasePicker',
  inheritAttr: false,
  props: {
    value: { type: String, required: true },
    icon: { type: String, required: false, default: null },
    iconColor: { type: String, required: false, default: null },
    readonly: { type: Boolean, required: false, default: false },
    disabled: { type: Boolean, required: false, default: false },
    filled: { type: Boolean, required: false, default: true },
    format: { type: Function, required: false, default: null },
    parse: { type: Function, required: false, default: null }
  },
  data () {
    return {
      pickerValue: this.value,
      stringValue: '',
      showPicker: false,
      textFieldIsActive: false,
      eventHandlers: {
        save: this.save,
        close: this.close,
        input: this.input
      }
    }
  },
  computed: {
    valueFormatted () {
      if (this.format != null) {
        return this.format(this.value)
      } else {
        return this.value
      }
    }
  },
  watch: {
    stringValue (val) {
      if (this.parse != null) {
        this.parse(val).then(this.setValue, this.setError)
      } else {
        this.setValue(val)
      }
    },
    value (val) {
      if (this.showPicker === false) {
        this.pickerValue = val
      }
    },
    valueFormatted (val) {
      if (this.textFieldIsActive === false) {
        this.stringValue = val
      }
    },
    textFieldIsActive (val) {
      if (val === false) {
        this.stringValue = this.valueFormatted
      }
    }
  },
  mounted () {
    this.stringValue = this.valueFormatted
  },
  methods: {
    setValue (val) {
      if (this.value !== val) {
        this.$emit('input', val)
        this.pickerValue = val
      }
    },
    setError (err) {
      console.log(err)
    },
    close () {
      this.showPicker = false
      this.pickerValue = this.value
    },
    save () {
      this.showPicker = false
      this.setValue(this.pickerValue)
    },
    input (val) {
      this.pickerValue = val
    }
  }
}
</script>
