<template>
  <ValidationProvider
    v-slot="{ errors: veeErrors }"
    ref="validationProvider"
    tag="div"
    :name="validationLabel"
    :vid="veeId ?? path"
    :rules="veeRules"
    :required="required"
    class="e-form-container"
  >
    <v-text-field
      ref="textField"
      v-bind="$attrs"
      :filled="filled"
      :required="required"
      :hide-details="hideDetails"
      :error-messages="veeErrors.concat(errorMessages)"
      :label="labelOrEntityFieldLabel"
      :class="[inputClass]"
      v-on="inputListeners"
    >
      <!-- passing through all slots -->
      <slot v-for="(_, name) in $slots" :slot="name" :name="name" />
      <template v-for="(_, name) in $scopedSlots" :slot="name" slot-scope="slotData">
        <slot :name="name" v-bind="slotData" />
      </template>
    </v-text-field>
  </ValidationProvider>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'

export default {
  name: 'ENumberField',
  components: { ValidationProvider },
  mixins: [formComponentPropsMixin, formComponentMixin],
  computed: {
    inputListeners: function () {
      const vm = this
      return Object.assign(
        {},
        // attach all $parent listeners
        this.$listeners,
        // override @input listener for correct handling of numeric values
        {
          input: function (value) {
            // Convert from 2,000.00 to 2000.00
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

            vm.$refs.textField.lazyValue = value
            vm.$emit('input', value)
          },
        }
      )
    },
  },
  methods: {
    focus() {
      this.$refs.textField.focus()
    },
  },
}
</script>

<style scoped>
[required]:deep(label::after) {
  content: '\a0*';
  font-size: 12px;
  color: #d32f2f;
}
[required]:deep(.v-input--is-label-active label::after) {
  color: gray;
}
</style>
