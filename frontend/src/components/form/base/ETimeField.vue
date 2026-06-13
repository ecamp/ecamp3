<template>
  <EParseField
    v-bind="$attrs"
    ref="input"
    :model-value="modelValue"
    :format="format"
    :parse="parse"
    :serialize="serialize"
    :deserialize="deserialize"
    :required="required"
    :vee-id="veeId"
    :vee-rules="veeRules"
    reset-on-blur
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <!-- passing through all slots -->
    <template v-for="(_, slot) of $slots" #[slot]="slotData">
      <slot :name="slot" v-bind="slotData"></slot>
    </template>
  </EParseField>
</template>

<script>
import { formComponentValidation } from '@/mixins/formComponentValidation.js'
import parseTime from '@/common/helpers/dayjs/parseTime.js'

export default {
  name: 'ETimeField',
  mixins: [formComponentValidation],
  props: {
    modelValue: { type: String, required: false, default: null },

    // format in which the `value` property is being provided & input events are triggered
    valueFormat: { type: [String, Array], default: 'HH:mm' },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      date: null,
    }
  },
  methods: {
    format(value) {
      if (typeof value === 'string') {
        return value
      }
      return !value ? '' : value.format('HH:mm')
    },
    /**
     * @param {string} value
     */
    parse(value) {
      if (value === '') {
        return null
      }
      value = value.trim()

      const { parsedDateTime, isValid } = parseTime(value)

      if (isValid) {
        return (
          this.date
            ?.hour(parsedDateTime.hour())
            .minute(parsedDateTime.minute())
            .second(parsedDateTime.second())
            .millisecond(parsedDateTime.millisecond()) ?? parsedDateTime
        )
      } else {
        throw new Error(this.$t('components.form.base.eTimeField.invalidFormat'))
      }
    },
    /**
     * @param {string|null} value
     */
    serialize(value) {
      try {
        return value?.format(this.valueFormat)
      } catch {
        return null
      }
    },
    /**
     * @param value {null|string}
     * @return {null|Color}
     */
    deserialize(value) {
      try {
        this.date = this.$date.utc(value, this.valueFormat)
        return !value ? null : this.date
      } catch {
        return null
      }
    },
    focus() {
      this.$refs.input.focus()
    },
    unmounted() {
      this.date = null
    },
  },
}
</script>
