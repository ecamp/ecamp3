<template>
  <EParseField
    ref="input"
    :value="value"
    :format="format"
    :parse="parse"
    :serialize="serialize"
    :deserialize="deserialize"
    :required="required"
    :vee-id="veeId"
    :vee-rules="veeRules"
    reset-on-blur
    v-bind="$attrs"
    v-on="$listeners"
    @input="$emit('input', $event)"
  >
    <!-- passing through all slots -->
    <slot v-for="(_, name) in $slots" :slot="name" :name="name" />
    <template v-for="(_, name) in $scopedSlots" :slot="name" slot-scope="slotData">
      <slot v-if="name !== 'prepend'" :name="name" v-bind="slotData" />
    </template>
  </EParseField>
</template>

<script>
import { formComponentMixin } from '@/mixins/formComponentMixin.js'
import parseTime from '@/common/helpers/dayjs/parseTime.js'

export default {
  name: 'ETimeField',
  mixins: [formComponentMixin],
  props: {
    value: { type: String, required: false, default: null },

    // format in which the `value` property is being provided & input events are triggered
    valueFormat: { type: [String, Array], default: 'HH:mm' },
  },
  emits: ['input'],
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
        throw new Error(this.$t('components.form.base.eTimeField.parseError'))
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
