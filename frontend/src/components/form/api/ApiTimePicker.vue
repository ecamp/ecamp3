<!--
Displays a field as a time picker + write access via API wrapper
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props">
    <time-picker
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      @input="wrapper.on.input">
      <status-icon v-slot:append :status="wrapper.status" />
    </time-picker>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import StatusIcon from './StatusIcon'
import TimePicker from '../base/TimePicker'

export default {
  name: 'ApiTimePicker',
  components: { ApiWrapper, StatusIcon, TimePicker },
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
