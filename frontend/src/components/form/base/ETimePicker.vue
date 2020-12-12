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

export default {
  name: 'ETimePicker',
  props: {
    icon: { type: String, required: false, default: 'mdi-clock-outline' },
    value: { type: [Number, String], required: true },
    valueFormat: { type: [String, Array], default: 'YYYY-MM-DDTHH:mmZ' }
  },
  data () {
    return {
      dateTime: null
    }
  },
  methods: {
    allowedStep: m => m % 15 === 0,
    format (val) {
      if (val !== '') {
        this.dateTime = this.$moment.utc(val, this.valueFormat)
        return this.dateTime.format('LT')
      }
      return ''
    },
    formatPicker (val) {
      if (val !== '') {
        return this.$moment.utc(val, this.valueFormat).format(this.$moment.HTML5_FMT.TIME)
      }
      return ''
    },
    parse (val) {
      if (val) {
        const m = this.$moment.utc(val, 'LT')
        this.dateTime.hours(m.hours()).minutes(m.minutes()).seconds(m.seconds()).milliseconds(m.milliseconds())
        if (m.isValid()) {
          return Promise.resolve(this.dateTime.format(this.valueFormat))
        } else {
          return Promise.reject(new Error('invalid format'))
        }
      } else {
        return Promise.resolve('')
      }
    },
    parsePicker (val) {
      if (val) {
        const m = this.$moment.utc(val, this.$moment.HTML5_FMT.TIME)
        this.dateTime.hours(m.hours()).minutes(m.minutes()).seconds(m.seconds()).milliseconds(m.milliseconds())
        if (m.isValid()) {
          return Promise.resolve(this.dateTime.format(this.valueFormat))
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
