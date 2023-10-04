<template>
  <ValidationProvider
    v-slot="{ errors: veeErrors }"
    ref="validationProvider"
    tag="div"
    :name="name"
    :vid="veeId"
    :rules="dynamicRules"
    :required="required"
    :mode="eagerIfChanged"
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
      :hide-spin-buttons="true"
      v-on="$listeners"
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
import { eagerIfChanged } from '@/helpers/veeValidateCustomInteractionMode'

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
  computed: {
    dynamicRules() {
      if (this.type !== 'number') return this.veeRules
      if (typeof this.veeRules === 'object') {
        const rule =
          this.$attrs.inputmode === 'decimal'
            ? { double: { separator: 'comma' } }
            : { numeric: true }
        // if there is an existing rule, don't overwrite
        return { ...this.veeRules, ...rule }
      }
      const rule = this.$attrs.inputmode === 'decimal' ? 'double:0comma' : 'numeric'
      return `${this.veeRules}${this.veeRules?.length === 0 ? '' : '|'}${rule}`
    },
  },
  methods: {
    eagerIfChanged,
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
