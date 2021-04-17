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
import BasePicker from './BasePicker.vue'

export default {
  name: 'ETimePicker',
  components: { BasePicker },
  props: {
    icon: { type: String, required: false, default: 'mdi-clock-outline' },
    value: { type: [Number, String], required: true },
    valueFormat: { type: [String, Array], default: 'YYYY-MM-DDTHH:mm:ssZ' }
  },
  data () {
    return {
      dateTime: null
    }
  },
  methods: {
    allowedStep: m => m % 15 === 0,
    setTime (dateTime) {
      if (this.dateTime && this.dateTime.isValid()) {
        this.dateTime = this.dateTime
          .hour(dateTime.hour())
          .minute(dateTime.minute())
          .second(dateTime.second())
          .millisecond(dateTime.millisecond())
      } else {
        this.dateTime = dateTime
      }
    },
    format (val) {
      if (val !== '') {
        this.dateTime = this.$date.utc(val, this.valueFormat)
        return this.dateTime.format('LT')
      }
      return ''
    },
    formatPicker (val) {
      if (val !== '') {
        return this.$date.utc(val, this.valueFormat).format(this.$date.HTML5_FMT.TIME)
      }
      return ''
    },
    parse (val) {
      if (val) {
        const parsedDateTime = this.$date.utc(val, 'LT')
        if (parsedDateTime.isValid() && parsedDateTime.format('LT') === val) {
          this.setTime(parsedDateTime)
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
        const parsedDateTime = this.$date.utc(val, this.$date.HTML5_FMT.TIME)
        if (parsedDateTime.isValid() && parsedDateTime.format(this.$date.HTML5_FMT.TIME) === val) {
          this.setTime(parsedDateTime)
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
