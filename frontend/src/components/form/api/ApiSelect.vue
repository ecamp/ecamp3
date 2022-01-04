<!--
Displays a field as a e-select + write access via API wrapper
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props"
    v-on="$listeners">
    <e-select
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="wrapper.readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving || wrapper.isLoading ? 'secondary' : false"
      :outlined="outlined"
      :filled="filled"
      :dense="dense"
      @input="wrapper.on.input">
      <template #append>
        <v-icon>mdi-menu-down</v-icon>
        <api-wrapper-append :wrapper="wrapper" />
      </template>
    </e-select>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin.js'
import ApiWrapper from './ApiWrapper.vue'
import ApiWrapperAppend from './ApiWrapperAppend.vue'

export default {
  name: 'ApiSelect',
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
