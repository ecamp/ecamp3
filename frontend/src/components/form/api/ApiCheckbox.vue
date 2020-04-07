<!--
Displays a field as a e-checkbox + write access via API wrapper

:loading doesn't work yet: https://github.com/vuetifyjs/vuetify/issues/7843
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props"
    separate-buttons>
    <e-checkbox
      :input-value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving"
      no-margin
      @change="wrapper.on.input">
      <template #append>
        <icon-success :visible="wrapper.status === 'success'" />
      </template>
    </e-checkbox>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import IconSuccess from './IconSuccess'

export default {
  name: 'ApiCheckbox',
  components: { ApiWrapper, IconSuccess },
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
