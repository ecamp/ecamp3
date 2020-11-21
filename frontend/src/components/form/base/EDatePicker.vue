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
    v-bind="$attrs"
    @input="$emit('input', $event)">
    <template slot-scope="picker">
      <v-date-picker
        :value="picker.value || ''"
        :locale="$i18n.locale"
        first-day-of-week="1"
        no-title
        scrollable
        @input="picker.on.input">
        <v-spacer />
        <v-btn text color="primary" @click="picker.on.close">Cancel</v-btn>
        <v-btn text color="primary" @click="picker.on.save">OK</v-btn>
      </v-date-picker>
    </template>

    <!-- passing the append slot through -->
    <template v-slot:append>
      <slot name="append" />
    </template>
  </base-picker>
</template>

<script>
import BasePicker from './BasePicker'

export default {
  name: 'DatePicker',
  components: { BasePicker },
  props: {
    value: { type: [String, Number], required: true },
    icon: { type: String, required: false, default: 'mdi-calendar' },
    valueFormat: { type: [String, Array], default: 'YYYY-MM-DD' }
  },
  methods: {
    format (val) {
      if (val !== '') {
        return this.$moment.utc(val, this.valueFormat, this.$i18n.locale).format('L')
      }
      return ''
    },
    formatPicker (val) {
      if (val !== '') {
        return this.$moment.utc(val, this.valueFormat).format(this.$moment.HTML5_FMT.DATE)
      }
      return ''
    },
    parse (val) {
      if (val) {
        const m = this.$moment.utc(val, 'L')
        if (m.isValid()) {
          return Promise.resolve(m.format(this.valueFormat))
        } else {
          switch (m.parsingFlags().overflow) {
            case 0: // Year
              return Promise.reject(new Error('invalid year'))
            case 1: // Month
              return Promise.reject(new Error('invalid month'))
            case 2: // Day
              return Promise.reject(new Error('invalid day'))
          }
          return Promise.reject(new Error('invalid format'))
        }
      } else {
        return Promise.resolve('')
      }
    },
    parsePicker (val) {
      if (val) {
        const m = this.$moment.utc(val, this.$moment.HTML5_FMT.DATE)
        if (m.isValid()) {
          return Promise.resolve(m.format(this.valueFormat))
        } else {
          return Promise.reject(new Error('invalid format'))
        }
      } else {
        return Promise.resolve('')
      }
    }
  }
}
</script>

<style scoped>
</style>
