<!--
Displays a field as a e-textarea + write access via API wrapper
-->

<template>
  <api-wrapper v-slot="wrapper" v-bind="$props" separate-buttons v-on="$listeners">
    <e-textarea
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="wrapper.readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving || wrapper.isLoading ? 'secondary' : false"
      :outlined="outlined"
      :filled="filled"
      :dense="dense"
      @input="wrapper.on.input"
    >
      <template #append>
        <api-wrapper-append :wrapper="wrapper" />
      </template>
    </e-textarea>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin.js'
import ApiWrapper from './ApiWrapper.vue'
import ApiWrapperAppend from './ApiWrapperAppend.vue'

export default {
  name: 'ApiTextarea',
  components: { ApiWrapper, ApiWrapperAppend },
  mixins: [apiPropsMixin],

  data() {
    return {}
  },
}
</script>

<style scoped></style>
