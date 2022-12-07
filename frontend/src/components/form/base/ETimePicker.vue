<!--
Displays a field as a time picker (can be used with v-model)
Allows 15min steps only
-->

<template>
  <base-picker
    :icon="icon"
    :value="value"
    :format="format"
    :format-picker="formatPicker"
    :parse="parse"
    :parse-picker="parsePicker"
    v-bind="$attrs"
    :vee-id="veeId"
    :vee-rules="veeRules"
    button-aria-label-i18n-key="components.form.base.eTimePicker.openPicker"
    @input="$emit('input', $event)"
  >
    <template #default="picker">
      <v-time-picker
        :value="picker.value || ''"
        :allowed-minutes="allowedStep"
        :format="$tc('global.datetime.vuetifyTimePickerFormat')"
        :min="min"
        :max="max"
        scrollable
        @input="picker.onInput"
      >
        <v-spacer />
        <v-btn text color="primary" @click="picker.close">
          {{ $tc('global.button.close') }}
        </v-btn>
      </v-time-picker>
    </template>

    <!-- passing the append slot through -->
    <template #append>
      <slot name="append" />
    </template>
  </base-picker>
</template>

<script>
import BasePicker from './BasePicker.vue'
import { HTML5_FMT } from '@/common/helpers/dateFormat.js'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'

export default {
  name: 'ETimePicker',
  components: { BasePicker },
  mixins: [formComponentMixin],
  props: {
    icon: { type: String, required: false, default: 'mdi-clock-outline' },
    value: { type: [Number, String], required: true },

    // format in which the `value` property is being provided & input events are triggered
    valueFormat: { type: [String, Array], default: 'YYYY-MM-DDTHH:mm:ssZ' },

    // v-time-picker props
    min: { type: String, default: null },
    max: { type: String, default: null },
  },
  methods: {
    allowedStep: (m) => m % 15 === 0,

    /**
     * override time on value but keep date
     */
    setTimeOnValue(time) {
      // current value as DayJS
      let valueDateTime = this.getValueAsDateTime(this.value)

      // override time
      if (valueDateTime && valueDateTime.isValid()) {
        valueDateTime = valueDateTime
          .hour(time.hour())
          .minute(time.minute())
          .second(time.second())
          .millisecond(time.millisecond())
      } else {
        valueDateTime = time
      }

      // return in value format
      return valueDateTime.format(this.valueFormat)
    },

    /**
     * returns val as DayJS object
     */
    getValueAsDateTime(val) {
      return this.$date.utc(val, this.valueFormat)
    },

    /**
     * Format internal value for display in the UI
     */
    format(val) {
      if (val !== '' && val !== null) {
        return this.getValueAsDateTime(val).format('LT')
      }
      return ''
    },

    /**
     * Format internal value for the popup component
     */
    formatPicker(val) {
      if (val !== '') {
        return this.getValueAsDateTime(val).format(HTML5_FMT.TIME)
      }
      return ''
    },

    /**
     * Parse a user-supplied value into the internal format
     */
    parse(val) {
      if (val) {
        const parsedDateTime = this.$date(val, 'LT')
        const formatted = parsedDateTime.format('LT')
        const valIgnoringLeadingZero = val.replace(/^0([1-9].+)/, '$1')
        if (parsedDateTime.isValid() && formatted === valIgnoringLeadingZero) {
          const newValue = this.setTimeOnValue(parsedDateTime)
          return Promise.resolve(newValue)
        } else {
          return Promise.reject(
            new Error(this.$tc('components.form.base.eTimePicker.invalidFormat'))
          )
        }
      } else {
        return Promise.resolve('')
      }
    },

    /**
     * Parse the value from the popup component into the internal format
     */
    parsePicker(val) {
      if (val) {
        const parsedDateTime = this.$date.utc(val, HTML5_FMT.TIME)
        if (parsedDateTime.isValid() && parsedDateTime.format(HTML5_FMT.TIME) === val) {
          const newValue = this.setTimeOnValue(parsedDateTime)
          return Promise.resolve(newValue)
        } else {
          return Promise.reject(new Error('invalid format'))
        }
      } else {
        return Promise.resolve('')
      }
    },
  },
}
</script>

<style scoped></style>
