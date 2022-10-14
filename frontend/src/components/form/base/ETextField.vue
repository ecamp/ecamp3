<template>
  <ValidationProvider
    v-slot="{ errors: veeErrors }"
    ref="validationProvider"
    tag="div"
    :name="name"
    :vid="veeId"
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
      :label="label || name"
      :class="[inputClass]"
      :type="type"
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
  name: 'ETextField',
  components: { ValidationProvider },
  mixins: [formComponentPropsMixin, formComponentMixin],
  props: {
    type: {
      type: String,
      default: 'text',
    },
  },
  data() {
    return {
      preventValidationOnBlur: false,
    }
  },
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
            vm.$data.preventValidationOnBlur = false
            if (vm.type === 'number') {
              vm.$emit('input', parseFloat(value))
            } else {
              vm.$emit('input', value)
            }
          },
          blur: function () {
            vm.$emit('blur')
            if (vm.$data.preventValidationOnBlur && vm.$refs.textField.value == '') {
              vm.$refs.validationProvider.reset()
            }
            vm.$data.preventValidationOnBlur = false
          },
        }
      )
    },
  },
  mounted() {
    this.preventValidationOnBlur =
      'autofocus' in this.$attrs && 'required' in this.$refs.validationProvider.$attrs
  },
  methods: {
    focus() {
      this.$refs.textField.focus()
    },
  },
}
</script>

<style scoped>
[required] >>> label::after {
  content: '\a0*';
  font-size: 12px;
  color: #d32f2f;
}
[required] >>> .v-input--is-label-active label::after {
  color: gray;
}
</style>
