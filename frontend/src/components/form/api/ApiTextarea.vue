<!--
Displays a field as a e-textarea + write access via API wrapper
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props"
    separate-buttons>
    <e-textarea
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving ? 'secondary' : false"
      outlined
      no-margin
      :filled="false"
      @input="wrapper.on.input"
      @blur="wrapper.on.touch">
      <template #append>
        <icon-success :visible="wrapper.status === 'success'" />
      </template>
    </e-textarea>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import IconSuccess from './IconSuccess'

export default {
  name: 'ApiTextarea',
  components: { ApiWrapper, IconSuccess },
  mixins: [apiPropsMixin],

  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
