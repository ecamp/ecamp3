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
    @input="$emit('input', $event)"
  >
    <template slot-scope="picker">
      <v-time-picker
        :value="picker.value || ''"
        :allowed-minutes="allowedStep"
        format="24hr"
        scrollable
        @input="picker.on.input"
      >
        <v-spacer />
        <v-btn text color="primary" data-testid="action-cancel" @click="picker.on.close">
          {{ $tc('global.button.cancel') }}
        </v-btn>
        <v-btn text color="primary" data-testid="action-ok" @click="picker.on.save">
          {{ $tc('global.button.ok') }}
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
      if (val !== '') {
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
        const parsedDateTime = this.$date.utc(val, 'LT')
        if (parsedDateTime.isValid() && parsedDateTime.format('LT') === val) {
          const newValue = this.setTimeOnValue(parsedDateTime)
          return Promise.resolve(newValue)
        } else {
          return Promise.reject(new Error('invalid format'))
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
