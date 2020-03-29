<!--
Displays a field as a e-checkbox + write access via API wrapper

:loading doesn't work yet: https://github.com/vuetifyjs/vuetify/issues/7843
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props">
    <e-checkbox
      :input-value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving"
      @change="wrapper.on.input">
      <template #append>
        <success-icon :visible="wrapper.status === 'success'" />
      </template>
    </e-checkbox>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import SuccessIcon from './SuccessIcon'

export default {
  name: 'ApiCheckbox',
  components: { ApiWrapper, SuccessIcon },
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
