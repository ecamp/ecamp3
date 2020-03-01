<!--
Displays a field as a date picker + write access via API wrapper
-->

<template>
  <api-wrapper
    v-bind="$props">
    <template slot-scope="wrapper">
      <date-picker
        :value="wrapper.localValue"
        v-bind="$attrs"
        :error-messages="wrapper.errorMessage"
        @input="wrapper.on.input">
        <status-icon slot="append" :status="wrapper.status" />
      </date-picker>
    </template>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import StatusIcon from './StatusIcon'
import DatePicker from './DatePicker'

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
