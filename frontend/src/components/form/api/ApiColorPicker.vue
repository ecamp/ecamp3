<!--
Displays a field as a color picker + write access via API wrapper
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props">
    <e-color-picker
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="readonly"
      :disabled="disabled"
      :loading="wrapper.isSaving ? 'secondary' : false"
      outlined
      :filled="false"
      :error-messages="wrapper.errorMessages"
      @input="wrapper.on.input">
      <template #append>
        <success-icon :visible="wrapper.status === 'success'" />
      </template>
    </e-color-picker>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import SuccessIcon from './SuccessIcon'

export default {
  name: 'ApiColorPicker',
  components: { ApiWrapper, SuccessIcon },
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
