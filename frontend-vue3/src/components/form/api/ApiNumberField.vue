<!--
Displays a field as a e-number-field + write access via API wrapper
-->

<template>
  <api-wrapper v-slot="wrapper" v-bind="$props" v-on="$listeners">
    <e-number-field
      ref="textField"
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
    </e-number-field>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin.js'
import ApiWrapper from './ApiWrapper.vue'
import ApiWrapperAppend from './ApiWrapperAppend.vue'

export default {
  name: 'ApiNumberField',
  components: { ApiWrapper, ApiWrapperAppend },
  mixins: [apiPropsMixin],
  props: {
    outlined: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {}
  },
  methods: {
    focus() {
      this.$refs.textField.focus()
    },
  },
}
</script>

<style scoped></style>
