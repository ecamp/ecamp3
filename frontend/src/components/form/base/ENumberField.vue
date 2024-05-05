<template>
  <EParseField
    ref="input"
    :value="value"
    :format="format"
    :parse="parse"
    :input-filter="inputFilter"
    :required="required"
    :vee-id="veeId"
    :vee-rules="veeRules"
    reset-on-blur
    v-bind="$attrs"
    @input="$emit('input', $event)"
  >
    <!-- passing through all slots -->
    <slot v-for="(_, name) in $slots" :slot="name" :name="name" />
  </EParseField>
</template>

<script>
import { formComponentMixin } from '@/mixins/formComponentMixin.js'

export default {
  name: 'ENumberField',
  mixins: [formComponentMixin],
  props: {
    value: { type: [String, Number], required: false, default: null },
  },
  emits: ['input'],
  methods: {
    format(value) {
      switch (value) {
        case null:
          return ''
        default:
          return value + ''
      }
    },
    inputFilter(value) {
      if (/\d/.test(value) && value.match(/^[^,]*,[^,.]+$/g)) {
        value = value.replace(/\./g, '').replace(/,/g, '.')
      }

      // Remove all dots except the first one
      let firstDotFound = false
      value = value.replace(/\./g, (match) =>
        firstDotFound ? '' : (firstDotFound = match)
      )

      // Remove everything except numbers, dots and the first minus sign
      const negative = value.startsWith('-')
      value = value.replace(/[^0-9.]/g, '')
      value = negative ? '-' + value : value
      return value
    },
    /**
     * @param {string} value
     */
    parse(value) {
      return isNaN(parseFloat(value)) || /^\.0*$/.test(value) ? null : parseFloat(value)
    },
    focus() {
      this.$refs.input.focus()
    },
  },
}
</script>
