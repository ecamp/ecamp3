<!--
Displays a field as a time picker (can be used with v-model)
Allows 15min steps only
-->

<template>
  <base-picker
    :icon="icon"
    :value="value"
    :format="format"
    :parse="parse"
    v-bind="$attrs"
    @input="$emit('input', $event)">
    <template slot-scope="picker">
      <v-time-picker
        :value="picker.value || ''"
        :allowed-minutes="allowedStep"
        format="24hr"
        scrollable
        @input="picker.on.input">
        <v-spacer />
        <v-btn text color="primary" @click="picker.on.close">Cancel</v-btn>
        <v-btn text color="primary" @click="picker.on.save">OK</v-btn>
      </v-time-picker>
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
  name: 'ETimePicker',
  components: { BasePicker },
  props: {
    icon: { type: String, required: false, default: 'mdi-clock-outline' },
    value: { type: String, required: true }
  },
  methods: {
    allowedStep: m => m % 15 === 0,
    format (val) {
      if (val !== '') {
        return this.$moment(val, this.$moment.HTML5_FMT.TIME, this.$i18n.locale).format('LT')
      }
      return ''
    },
    parse (val) {
      if (val) {
        const m = this.$moment(val, [this.$moment.HTML5_FMT.TIME, 'LT'])
        if (m.isValid()) {
          return Promise.resolve(m.format(this.$moment.HTML5_FMT.TIME))
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
    }
  }
}
</script>

<style scoped>
</style>
