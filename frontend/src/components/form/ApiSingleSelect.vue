<!--
Displays a field as text or as an input field, depending on the editing prop.
You can two-way bind to the value using v-model.
-->

<template>
  <api-wrapper
    v-bind="$props">
    <template slot-scope="wrapper">
      <v-select
        v-model="wrapper.localValue"
        v-bind="$attrs"
        :label="label"
        :error-messages="wrapper.errorMessage"
        @input="wrapper.on.input"
        @blur="wrapper.on.touch">
        <status-icon slot="append" :status="wrapper.status" />
      </v-select>
    </template>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import ApiWrapper from './ApiWrapper'
import StatusIcon from './StatusIcon'

export default {
  name: 'ApiSingleSelect',
  components: { ApiWrapper, StatusIcon },
  mixins: [apiPropsMixin],
  props: {
    // disable delay in default for select component
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
