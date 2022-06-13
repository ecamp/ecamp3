<!--
Displays a field as a time picker + write access via API wrapper
-->

<template>
  <api-wrapper v-slot="wrapper" v-bind="$props" v-on="$listeners">
    <e-time-picker
      :value="wrapper.localValue || ''"
      v-bind="$attrs"
      :readonly="wrapper.readonly"
      :disabled="disabled"
      :loading="wrapper.isSaving || wrapper.isLoading ? 'secondary' : false"
      :outlined="outlined"
      :filled="filled"
      :error-messages="wrapper.errorMessages"
      @input="wrapper.on.input">
      <template #append>
        <api-wrapper-append :wrapper="wrapper" />
      </template>
    </e-time-picker>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin.js'
import ApiWrapper from './ApiWrapper.vue'
import ApiWrapperAppend from './ApiWrapperAppend.vue'

export default {
  name: 'ApiTimePicker',
  components: { ApiWrapper, ApiWrapperAppend },
  mixins: [apiPropsMixin],
  data () {
    return {}
  }
}
</script>

<style scoped></style>
