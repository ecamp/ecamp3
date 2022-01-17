<template>
  <ValidationProvider
    v-slot="{ errors: veeErrors }"
    tag="div"
    :name="name"
    :vid="veeId"
    :rules="veeRules"
    class="e-form-container">
    <v-text-field
      ref="textField"
      v-bind="$attrs"
      :filled="filled"
      :hide-details="hideDetails"
      :error-messages="veeErrors.concat(errorMessages)"
      :label="label || name"
      :class="[inputClass]"
      :type="type"
      v-on="inputListeners">
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

export default {
  name: 'ETextField',
  components: { ValidationProvider },
  mixins: [formComponentPropsMixin],
  props: {
    type: {
      type: String,
      default: 'text'
    }
  },
  computed: {
    inputListeners: function () {
      const vm = this
      return Object.assign({},
        // attach all $parent listeners
        this.$listeners,

        // override @input listener for correct handling of numeric values
        {
          input: function (value) {
            if (vm.type === 'number') {
              vm.$emit('input', parseFloat(value))
            } else {
              vm.$emit('input', value)
            }
          }
        }
      )
    }
  },
  methods: {
    focus () {
      this.$refs.textField.focus()
    }
  }
}
</script>
