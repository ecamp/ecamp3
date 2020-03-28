<!--
Displays a field as a v-textarea + write access via API wrapper
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props">
    <v-textarea
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      hide-details="auto"
      outlined
      @input="wrapper.on.input"
      @blur="wrapper.on.touch">
      <status-icon v-slot:append :status="wrapper.status" />
    </v-textarea>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import StatusIcon from './StatusIcon'

export default {
  name: 'ApiTextarea',
  components: { ApiWrapper, StatusIcon },
  mixins: [apiPropsMixin],

  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
