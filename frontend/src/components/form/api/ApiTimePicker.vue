<!--
Displays a field as a time picker + write access via API wrapper
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props">
    <e-time-picker
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="wrapper.readonly"
      :disabled="disabled"
      :loading="wrapper.isSaving || wrapper.isLoading ? 'secondary' : false"
      outlined
      no-margin
      :filled="false"
      :error-messages="wrapper.errorMessages"
      @input="wrapper.on.input">
      <template #append>
        <api-wrapper-append :wrapper="wrapper" />
      </template>
    </e-time-picker>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import ApiWrapperAppend from './ApiWrapperAppend'

export default {
  name: 'ApiTimePicker',
  components: { ApiWrapper, ApiWrapperAppend },
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
