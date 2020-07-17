<!--
Displays a field as a date picker (can be used with v-model)
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
    value: { type: String, required: true },
    icon: { type: String, required: false, default: 'mdi-calendar' }
  },
  methods: {
    format (val) {
      if (val !== '') {
        return this.$moment(val, this.$moment.HTML5_FMT.DATE, this.$i18n.locale).format('L')
      }
      return ''
    },
    parse (val) {
      if (val) {
        const m = this.$moment(val, 'L')
        if (m.isValid()) {
          return Promise.resolve(m.format(this.$moment.HTML5_FMT.DATE))
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
