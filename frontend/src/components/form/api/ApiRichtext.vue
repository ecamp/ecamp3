<!--
Displays a field as a e-textarea + write access via API wrapper
-->

<template>
  <api-wrapper v-slot="wrapper" v-bind="$props" separate-buttons v-on="$listeners">
    <e-richtext
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="wrapper.readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving || wrapper.isLoading ? 'secondary' : false"
      :outlined="outlined"
      :filled="filled"
      @input="wrapper.on.input"
    >
      <template #append>
        <api-wrapper-append :wrapper="wrapper" />
      </template>
    </e-richtext>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin.js'
import ApiWrapper from './ApiWrapper.vue'
import ApiWrapperAppend from './ApiWrapperAppend.vue'

export default {
  name: 'ApiRichtext',
  components: { ApiWrapper, ApiWrapperAppend },
  mixins: [apiPropsMixin],
}
</script>

<style scoped></style>
