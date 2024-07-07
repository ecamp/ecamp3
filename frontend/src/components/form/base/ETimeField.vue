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

export default {
  name: 'ETimeField',
  mixins: [formComponentMixin],
  props: {
    value: { type: String, required: false, default: null },
  },
  emits: ['input'],
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
      try {
        if (!value.match(/[.:,h\s]+/)) {
          if (Number.isNaN(parseInt(value))) {
            throw new Error('Invalid number')
          }
          if (value.length === 1) {
            return this.$date({ hour: this.parseHour(value) })
          }
          if (value.length === 2) {
            const hour = parseInt(value)
            if (0 <= hour && hour < 24) {
              return this.$date({ hour })
            } else {
              return this.$date({
                hour: this.parseHour(value.slice(0, 1)),
                minute: this.parseMinute(value.slice(1, 2)),
              })
            }
          }
          if (value.length === 3) {
            const hour = parseInt(value.slice(0, 2))
            if (hour >= 24) {
              return this.$date({
                hour: this.parseHour(value.slice(0, 1)),
                minute: this.parseMinute(value.slice(1, 3)),
              })
            }
            const minute = parseInt(value.slice(2, 3))
            if (minute >= 6) {
              return this.$date({
                hour: this.parseHour(value.slice(0, 2)),
                minute: this.parseMinute(value.slice(2, 3).padStart(2, '0')),
              })
            } else {
              return this.$date({
                hour: this.parseHour(value.slice(0, 2)),
                minute: this.parseMinute(value.slice(2, 3).padEnd(2, '0')),
              })
            }
          }
          if (value.length === 4) {
            const hour = parseInt(value.slice(0, 2))
            if (hour >= 24) {
              return null
            }
            return this.$date({
              hour,
              minute: parseInt(value.slice(2, 4)),
            })
          }
        } else {
          return this.$date(value, ['H:m:s', 'H:m', 'H[h]m'])
        }
        return null
      } catch (e) {
        if (e instanceof TypeError) {
          throw new Error(this.$tc('components.form.base.eColorField.parseError'))
        } else {
          throw e
        }
      }
    },
    /**
     * @param {string|null} value
     */
    serialize(value) {
      try {
        return value?.format('HH:mm')
      } catch (e) {
        return null
      }
    },
    /**
     * @param value {null|string}
     * @return {null|Color}
     */
    deserialize(value) {
      try {
        return !value ? null : this.$date.utc(value, 'HH:mm')
      } catch (e) {
        return null
      }
    },
    focus() {
      this.$refs.input.focus()
    },
    parseHour(value) {
      const hour = parseInt(value)

      if (Number.isNaN(hour)) throw new Error('Invalid minute')

      if (value.length === 1) {
        return hour
      }
      if (0 <= hour && hour < 24) return hour
      throw new Error('Invalid hour')
    },
    parseMinute(value) {
      const minute = parseInt(value)

      if (Number.isNaN(minute)) throw new Error('Invalid minute')

      if (value.length === 1) {
        if (minute < 6) {
          return parseInt(value.padEnd(2, '0'))
        } else {
          return minute
        }
      }
      if (0 <= minute && minute < 60) return minute
      throw new Error('Invalid minute')
    },
  },
}
</script>
