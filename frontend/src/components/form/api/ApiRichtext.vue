<!--
Displays a field as a e-richtext + write access via API wrapper
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
      :loading="wrapper.isSaving || wrapper.isLoading"
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
import ERichtext from '../base/ERichtext'
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import ApiWrapperAppend from './ApiWrapperAppend'

export default {
  name: 'ApiRichtext',
  components: { ApiWrapper, ApiWrapperAppend, ERichtext },
  mixins: [apiPropsMixin]
}
</script>

<style scoped>
</style>
