<!--
Displays a field as a date picker (can be used with v-model)
-->

<template>
  <base-picker
    v-bind="$attrs"
    :icon="icon"
    :model-value="modelValue"
    :format="format"
    :format-picker="formatPicker"
    :parse="parse"
    :parse-picker="parsePicker"
    :required="required"
    :vee-id="veeId"
    :vee-rules="veeRules"
    button-aria-label-i18n-key="components.form.base.eDatePicker.openPicker"
    close-on-picker-input
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <template #default="picker">
      <v-date-picker
        :picker-date.sync="pickerMonth"
        :model-value="picker.value || ''"
        :locale="$i18n.locale"
        first-day-of-week="1"
        :min="min"
        :max="max"
        :allowed-dates="allowedDates"
        no-title
        scrollable
        show-adjacent-months
        @update:model-value="picker.onInput"
      >
        <v-spacer />
        <v-btn color="primary" variant="text" @click="picker.close">
          {{ $t('global.button.close') }}
        </v-btn>
      </v-date-picker>
    </template>

    <!-- passing the append-inner slot through -->
    <template #append-inner>
      <slot name="append-inner" />
    </template>
  </base-picker>
</template>

<script>
import BasePicker from './BasePicker.vue'
import { HTML5_FMT } from '@/helpers/dateFormat.js'
import { formComponentValidation } from '@/mixins/formComponentValidation.js'
import dayjs from '@/helpers/dayjs.js'

export default {
  name: 'DatePicker',
  components: { BasePicker },
  mixins: [formComponentValidation],
  props: {
    modelValue: { type: [String, Number], required: true },
    icon: { type: String, required: false, default: 'mdi-calendar' },

    // format in which the `value` property is being provided & input events are triggered
    valueFormat: { type: [String, Array], default: 'YYYY-MM-DD' },

    // v-date-picker props
    allowedDates: { type: Function, default: null },
    min: { type: String, default: null },
    max: { type: String, default: null },
  },
  emits: ['update:modelValue'],
  data: () => ({
    pickerMonth: undefined,
  }),
  watch: {
    min: {
      handler(newMin) {
        if (this.modelValue) return
        if (!newMin) return
        const currentPickerMonth = this.$date(this.pickerMonth)
        const newMinPickerMonth = this.$date(newMin)
        if (currentPickerMonth.unix() < newMinPickerMonth.unix()) {
          // Update the month displayed in the picker
          this.pickerMonth = newMinPickerMonth.format('YYYY-MM')
        }
      },
      immediate: true,
    },
    max: {
      handler(newMax) {
        if (this.modelValue) return
        if (!newMax) return
        const currentPickerMonth = this.$date(this.pickerMonth)
        const newMaxPickerMonth = this.$date(newMax)
        if (currentPickerMonth.unix() > newMaxPickerMonth.unix()) {
          // Update the month displayed in the picker
          this.pickerMonth = newMaxPickerMonth.format('YYYY-MM')
        }
      },
      immediate: true,
    },
  },
  methods: {
    /**
     * override date but keep time
     */
    setDateOnValue(date) {
      // current value as DayJS
      let valueDateTime = this.getValueAsDateTime(this.modelValue)

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
      if (val !== '' && val !== null) {
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
        const parsedDate = this.$date(val, ['L', 'l'])
        if (
          parsedDate.isValid() &&
          (parsedDate.format('L') === val || parsedDate.format('l') === val)
        ) {
          const newValue = this.setDateOnValue(parsedDate)
          return Promise.resolve(newValue)
        } else {
          return Promise.reject(
            new Error(this.$t('components.form.base.eDatePicker.invalidFormat'))
          )
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
        const date = val // TODO: check if we now have a timezone issue across date bounderies
        if (date && dayjs.isDayjs(date) && date.isValid()) {
          const newValue = this.setDateOnValue(date)
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
