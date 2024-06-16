<!--
Displays a field as a e-color-field + write access via API wrapper
-->

<template>
  <api-wrapper v-slot="wrapper" v-bind="$props" separate-buttons v-on="$listeners">
    <e-color-field
      :value="wrapper.localValue"
      v-bind="$attrs"
      :path="path"
      :readonly="wrapper.readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving || wrapper.isLoading ? 'secondary' : false"
      :outlined="outlined"
      :filled="filled"
      :dense="dense"
      @input="wrapper.on.input"
      @blur="wrapper.on.blur"
    >
      <template #append>
        <api-wrapper-append :wrapper="wrapper" />
      </template>
    </e-color-field>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin.js'
import ApiWrapper from './ApiWrapper.vue'
import ApiWrapperAppend from './ApiWrapperAppend.vue'

export default {
  name: 'ApiColorField',
  components: { ApiWrapper, ApiWrapperAppend },
  mixins: [apiPropsMixin],

  data() {
    return {}
  },
}
</script>

<style scoped></style>
