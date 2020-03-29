<!--
Displays a field as a e-textarea + write access via API wrapper
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props">
    <e-textarea
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving ? 'secondary' : false"
      outlined
      :filled="false"
      @input="wrapper.on.input"
      @blur="wrapper.on.touch">
      <template #append>
        <success-icon :visible="wrapper.status === 'success'" />
      </template>
    </e-textarea>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import SuccessIcon from './SuccessIcon'

export default {
  name: 'ApiTextarea',
  components: { ApiWrapper, SuccessIcon },
  mixins: [apiPropsMixin],

  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
