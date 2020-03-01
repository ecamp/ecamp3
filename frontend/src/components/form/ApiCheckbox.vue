<!--
Displays a field as a v-checkbox + write access via API wrapper
-->

<template>
  <api-wrapper
    v-bind="$props">
    <template slot-scope="wrapper">
      <v-checkbox
        :value="wrapper.localValue"
        v-bind="$attrs"
        :error-messages="wrapper.errorMessage"
        @click="wrapper.on.input">
        <status-icon slot="append" :status="wrapper.status" />
      </v-checkbox>
    </template>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import StatusIcon from './StatusIcon'

export default {
  name: 'ApiCheckbox',
  components: { ApiWrapper, StatusIcon },
  mixins: [apiPropsMixin],
  props: {
    // disable delay per default
    autoSaveDelay: { type: Number, default: 0, required: false }
  },
  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
