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
        :readonly="readonly"
        :disabled="disabled"
        :error-messages="wrapper.errorMessages"
        :loading="wrapper.isSaving"
        hide-details="auto"
        outlined
        @input="wrapper.on.input"
        @blur="wrapper.on.touch" />
    </template>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'

export default {
  name: 'ApiTextField',
  components: { ApiWrapper },
  mixins: [apiPropsMixin],

  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
