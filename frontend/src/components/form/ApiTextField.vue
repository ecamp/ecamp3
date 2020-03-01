<!--
Displays a field as a v-text-field + write access via API wrapper
-->

<template>
  <api-wrapper
    v-bind="$props">
    <template slot-scope="wrapper">
      <v-text-field
        :value="wrapper.localValue"
        v-bind="$attrs"
        :error-messages="wrapper.errorMessage"
        @input="wrapper.on.input"
        @blur="wrapper.on.touch">
        <status-icon slot="append" :status="wrapper.status" />
      </v-text-field>
    </template>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import StatusIcon from './StatusIcon'

export default {
  name: 'ApiTextField',
  components: { ApiWrapper, StatusIcon },
  mixins: [apiPropsMixin],

  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
