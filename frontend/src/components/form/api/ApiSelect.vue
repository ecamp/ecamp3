<!--
Displays a field as a e-select + write access via API wrapper
-->

<template>
  <api-wrapper
    v-slot="wrapper"
    v-bind="$props">
    <e-select
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="wrapper.readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving || wrapper.isLoading ? 'secondary' : false"
      outlined
      no-margin
      :filled="false"
      @input="wrapper.on.input"
      @blur="wrapper.on.touch">
      <template #append>
        <v-icon>mdi-menu-down</v-icon>
        <api-wrapper-append :wrapper="wrapper" />
      </template>
    </e-select>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import ApiWrapperAppend from './ApiWrapperAppend'

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
