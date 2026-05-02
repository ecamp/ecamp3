<template>
  <ValidationField
    v-slot="{ handleChange, handleBlur, handleReset, errors: veeFieldErrors }"
    ref="validationField"
    :model-value="modelValue"
    :name="veeId ?? path ?? validationLabel"
    :label="validationLabel"
    :vee-rules="veeRules"
  >
    <v-text-field
      ref="textField"
      :model-value="modelValue"
      :class="[inputClass]"
      :error-messages="(veeFieldErrors ?? []).concat(errorMessages)"
      :label="labelOrEntityFieldLabel"
      :type="type"
      v-bind="$attrs"
      :required="required"
      :autofocus
      :hide-details="hideDetails"
      @blur="onBlur($event, handleReset, handleBlur)"
      @update:model-value="onInput($event, handleChange)"
    >
      <!-- passing through all slots -->
      <template v-for="(_, slot) of $slots" #[slot]="slotData">
        <slot :name="slot" v-bind="slotData || {}"></slot>
      </template>
    </v-text-field>
  </ValidationField>
</template>

<script>
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { formComponentValidation } from '@/mixins/formComponentValidation.js'
import ValidationField from './ValidationField.vue'

export default {
  name: 'ETextField',
  components: { ValidationField },
  mixins: [formComponentPropsMixin, formComponentValidation],
  props: {
    modelValue: { type: String, required: false, default: null },
    autofocus: { type: Boolean, required: false, default: false },
    type: {
      type: String,
      default: 'text',
    },
  },
  emits: ['update:modelValue', 'blur'],
  data() {
    return {
      preventValidationOnBlur: false,
    }
  },
  mounted() {
    this.preventValidationOnBlur =
      'autofocus' in this.$attrs &&
      this.veeRules?.includes('required') &&
      this.modelValue == ''
  },
  methods: {
    focus() {
      this.$refs.textField.focus()
    },
    onInput(event, handleChange) {
      this.preventValidationOnBlur = false
      handleChange(event)
      this.$emit('update:modelValue', event)
    },
    onBlur(event, handleReset, handleBlur) {
      this.$emit('blur', event)
      if (this.preventValidationOnBlur) {
        handleReset()
      } else {
        handleBlur()
      }
      this.preventValidationOnBlur = false
    },
  },
}
</script>
