<!--
Displays a field as a e-text-field + write access via API wrapper
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props">
    <e-text-field
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="readonly || wrapper.readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving || wrapper.isLoading ? 'secondary' : false"
      no-margin
      outlined
      :filled="false"
      @input="wrapper.on.input"
      @blur="wrapper.on.touch">
      <template #append>
        <api-wrapper-append :wrapper="wrapper" />
      </template>
    </e-text-field>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import ApiWrapperAppend from './ApiWrapperAppend'

export default {
  name: 'ApiTextField',
  components: { ApiWrapper, ApiWrapperAppend },
  mixins: [apiPropsMixin],

  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
