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
    <template #prepend="props">
      <slot name="prepend" v-bind="props">
        <ColorSwatch
          class="mt-n1"
          :color="props.serializedValue"
          tag="div"
          :aria-label="props.serializedValue"
        />
      </slot>
    </template>
  </EParseField>
</template>

<script>
import { reactive } from 'vue'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'
import { parse, serialize, ColorSpace, sRGB } from 'colorjs.io/fn'
import ColorSwatch from '@/components/form/base/ColorPicker/ColorSwatch.vue'

export default {
  name: 'EColorField',
  components: { ColorSwatch },
  mixins: [formComponentMixin],
  props: {
    value: { type: String, required: false, default: null },
  },
  emits: ['input'],
  setup() {
    if (!('srgb' in ColorSpace.registry)) {
      ColorSpace.register(sRGB)
    }
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
        return serialize(value, { format: 'hex', collapse: false }).toUpperCase()
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
        return !value ? null : reactive(parse(value, { space: 'srgb', format: 'hex' }))
      } catch (e) {
        return null
      }
    },
    focus() {
      this.$refs.input.focus()
    },
  },
}
</script>
