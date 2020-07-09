<!--
Displays a field as a e-textarea + write access via API wrapper
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props"
    separate-buttons>
    <e-richtext
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="wrapper.readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving || wrapper.isLoading ? 'secondary' : false"
      outlined
      :my="0"
      :filled="false"
      @input="wrapper.on.input">
      <template #append>
        <api-wrapper-append :wrapper="wrapper" />
      </template>
    </e-richtext>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import ApiWrapperAppend from './ApiWrapperAppend'

export default {
  name: 'ApiRichtext',
  components: { ApiWrapper, ApiWrapperAppend },
  mixins: [apiPropsMixin],

  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
