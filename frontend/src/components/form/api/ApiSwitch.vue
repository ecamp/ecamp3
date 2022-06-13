<!--
Displays a field as a e-checkbox + write access via API wrapper

:loading doesn't work yet: https://github.com/vuetifyjs/vuetify/issues/7843
-->

<template>
  <api-wrapper v-slot="wrapper" v-bind="$props"
               separate-buttons
               v-on="$listeners">
    <e-switch
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="wrapper.readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving"
      @input="wrapper.on.input">
      <template #append>
        <api-wrapper-append :wrapper="wrapper" />
      </template>
    </e-switch>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin.js'
import ApiWrapper from './ApiWrapper.vue'
import ApiWrapperAppend from './ApiWrapperAppend.vue'

export default {
  name: 'ApiSwitch',
  components: { ApiWrapper, ApiWrapperAppend },
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

<style scoped></style>
