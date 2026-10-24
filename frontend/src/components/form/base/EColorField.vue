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
    <template v-for="(_, slot) of filteredSlots" #[slot]="slotData">
      <slot :name="slot" v-bind="slotData || {}"></slot>
    </template>
    <template #prepend="slotData">
      <slot name="prepend" v-bind="slotData">
        <ColorSwatch
          class="mt-n1"
          :color="slotData.serializedValue"
          tag="div"
          :aria-label="slotData.serializedValue"
        />
      </slot>
    </template>
  </EParseField>
</template>

<script>
import { reactive } from 'vue'
import { formComponentValidation } from '@/mixins/formComponentValidation.js'
import { parse, serialize } from 'colorjs.io/fn'
import ColorSwatch from '@/components/form/base/ColorPicker/ColorSwatch.vue'

export default {
  name: 'EColorField',
  components: { ColorSwatch },
  mixins: [formComponentValidation],
  props: {
    modelValue: { type: String, required: false, default: null },
  },
  emits: ['update:modelValue'],
  computed: {
    filteredSlots() {
      return Object.fromEntries(
        Object.entries(this.$slots).filter((_, slot) => slot !== 'prepend')
      )
    },
  },
  methods: {
    format(value) {
      if (typeof value === 'string') {
        return value
      }
      return !value
        ? ''
        : serialize(value, {
            space: 'srgb',
            format: 'hex',
            collapse: false,
          }).toUpperCase()
    },
    /**
     * @param {string} value
     */
    parse(value) {
      if (value === '') {
        return null
      }
      try {
        const color = parse(value)
        color.alpha = 1
        return reactive(color)
      } catch (e) {
        if (e instanceof TypeError) {
          throw new Error(this.$t('components.form.base.eColorField.parseError'), {
            cause: e,
          })
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
        return serialize(value, { format: 'hex', collapse: false }).toUpperCase()
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
        return !value ? null : reactive(parse(value, { space: 'srgb', format: 'hex' }))
      } catch {
        return null
      }
    },
    focus() {
      this.$refs.input.focus()
    },
  },
}
</script>
