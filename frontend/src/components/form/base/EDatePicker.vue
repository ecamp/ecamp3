<!--
Displays a field as a date picker (can be used with v-model)
-->

<template>
  <base-picker
    :icon="icon"
    :value="value"
    :format="format"
    :format-picker="formatPicker"
    :parse="parse"
    :parse-picker="parsePicker"
    :required="required"
    :vee-id="veeId"
    :vee-rules="veeRules"
    v-bind="$attrs"
    @input="$emit('input', $event)"
  >
    <template slot-scope="picker">
      <v-date-picker
        :value="picker.value || ''"
        :locale="$i18n.locale"
        first-day-of-week="1"
        :allowed-dates="allowedDates"
        no-title
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
      </v-date-picker>
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
  name: 'DatePicker',
  components: { BasePicker },
  mixins: [formComponentMixin],
  props: {
    value: { type: [String, Number], required: true },
    icon: { type: String, required: false, default: 'mdi-calendar' },

    // format in which the `value` property is being provided & input events are triggered
    valueFormat: { type: [String, Array], default: 'YYYY-MM-DD' },

    // v-date-picker allowedDates
    allowedDates: { type: Function, default: null },
  },
  methods: {
    /**
     * override date but keep time
     */
    setDateOnValue(date) {
      // current value as DayJS
      let valueDateTime = this.getValueAsDateTime(this.value)

      // override date
      if (valueDateTime && valueDateTime.isValid()) {
        valueDateTime = valueDateTime
          .year(date.year())
          .month(date.month())
          .date(date.date())
      } else {
        valueDateTime = date
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
        return this.getValueAsDateTime(val).format('L')
      }
      return ''
    },

    /**
     * Format internal value for the popup component. If omitted, uses format instead.
     */
    formatPicker(val) {
      if (val !== '') {
        return this.getValueAsDateTime(val).format(HTML5_FMT.DATE)
      }
      return ''
    },

    /**
     * Parse a user-supplied value into the internal format
     */
    parse(val) {
      if (val) {
        const parsedDate = this.$date.utc(val, 'L')
        if (parsedDate.isValid() && parsedDate.format('L') === val) {
          const newValue = this.setDateOnValue(parsedDate)
          return Promise.resolve(newValue)
        } else {
          return Promise.reject(new Error('invalid format'))
        }
      } else {
        return Promise.resolve('')
      }
    },

    /**
     * Parse the value from the popup component into the internal format. If omitted, uses parse instead.
     */
    parsePicker(val) {
      if (val) {
        const parsedDate = this.$date.utc(val, HTML5_FMT.DATE)
        if (parsedDate.isValid() && parsedDate.format(HTML5_FMT.DATE) === val) {
          const newValue = this.setDateOnValue(parsedDate)
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
