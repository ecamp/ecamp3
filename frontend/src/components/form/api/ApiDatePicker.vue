<!--
Displays a field as a date picker + write access via API wrapper
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props">
    <date-picker
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      @input="wrapper.on.input">
      <status-icon v-slot:append :status="wrapper.status" />
    </date-picker>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import StatusIcon from './StatusIcon'
import DatePicker from '../base/DatePicker'

export default {
  name: 'ApiDatePicker',
  components: { ApiWrapper, StatusIcon, DatePicker },
  mixins: [apiPropsMixin],
  props: {
    // disable delay per default
    autoSaveDelay: { type: Number, default: 0, required: false }
  },
  data () {
    return {
    }
  }
}
</script>

<style scoped>
</style>
