<!--
Displays a field as a e-text-field + write access via API wrapper
-->

<template>
  <api-wrapper v-slot="wrapper" v-bind="$props" v-on="$listeners">
    <e-text-field
      ref="textField"
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
        <api-wrapper-append :wrapper="wrapper" />
      </template>
    </e-text-field>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin.js'
import ApiWrapper from './ApiWrapper.vue'
import ApiWrapperAppend from './ApiWrapperAppend.vue'

export default {
  name: 'ApiTextField',
  components: { ApiWrapper, ApiWrapperAppend },
  mixins: [apiPropsMixin],
  props: {
    outlined: {
      type: Boolean,
      default: true
    }
  },
  data () {
    return {}
  },
  methods: {
    focus () {
      this.$refs.textField.focus()
    }
  }
}
</script>

<style scoped></style>
