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
        <v-btn text color="primary"
               data-testid="action-cancel"
               @click="picker.on.close">
          {{ $tc('global.button.cancel') }}
        </v-btn>
        <v-btn text color="primary"
               data-testid="action-ok"
               @click="picker.on.save">
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
    parseTime (value) {
      if (this.valueFormat === 'x') {
        return this.$date.utc(value)
      } else {
        return this.$date.utc(value, this.valueFormat)
      }
    },
    formatTime (time) {
      if (this.valueFormat === 'x') {
        return time.valueOf()
      } else {
        return time.format(this.valueFormat)
      }
    },
    setTime (initialDateTime, newDateTime) {
      return initialDateTime
        .hour(newDateTime.hour())
        .minute(newDateTime.minute())
        .second(newDateTime.second())
        .millisecond(newDateTime.millisecond())
    },
    format (val) {
      if (val !== '') {
        this.dateTime = this.parseTime(val)
        return this.dateTime.format('LT')
      }
      return ''
    },
    formatPicker (val) {
      if (val !== '') {
        return this.parseTime(val).format('HH:mm')
      }
      return ''
    },
    parse (val) {
      if (val) {
        const parsedDateTime = this.$date.utc(val, 'LT')
        if (parsedDateTime.isValid() && parsedDateTime.format('LT') === val) {
          this.dateTime = this.setTime(this.dateTime, parsedDateTime)
          return Promise.resolve(this.formatTime(this.dateTime))
        } else {
          return Promise.reject(new Error('invalid format'))
        }
      } else {
        return Promise.resolve('')
      }
    },
    parsePicker (val) {
      if (val) {
        const parsedDateTime = this.$date.utc(val, 'HH:mm')
        if (parsedDateTime.isValid() && parsedDateTime.format('HH:mm') === val) {
          this.dateTime = this.setTime(this.dateTime, parsedDateTime)
          return Promise.resolve(this.formatTime(this.dateTime))
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
