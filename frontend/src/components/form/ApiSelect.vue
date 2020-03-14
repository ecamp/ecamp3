<!--
Displays a field as a v-select + write access via API wrapper
-->

<template>
  <api-wrapper
    v-bind="$props">
    <template slot-scope="wrapper">
      <v-select
        :value="wrapper.localValue"
        v-bind="$attrs"
        :readonly="readonly"
        :disabled="disabled"
        :error-messages="wrapper.errorMessages"
        hide-details="auto"
        outlined
        @input="wrapper.on.input"
        @blur="wrapper.on.touch">
        <status-icon slot="append" :status="wrapper.status" />
      </v-select>
    </template>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import StatusIcon from './StatusIcon'

export default {
  name: 'ApiSelect',
  components: { ApiWrapper, StatusIcon },
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
