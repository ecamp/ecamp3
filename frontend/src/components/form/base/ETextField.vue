<template>
  <div class="e-form-container">
    <v-text-field
      v-bind="$attrs"
      :id="name"
      ref="textField"
      :name="name"
      :type="type"
      :model-value="inputValue"
      :error-messages="errorMessage"
      :label="label || name"
      :class="[inputClass]"
      :filled="filled"
      :required="required"
      :hide-details="hideDetails"
      @update:model-value="handleChange"
      @blur="handleBlur"
    >
      <!-- passing through all slots -->
      <template v-for="(_, name) in $slots" #[name]="slotData">
        <slot :name="name" v-bind="slotData" />
      </template>
    </v-text-field>
  </div>
</template>

<script setup>
import { toRef } from 'vue'
import { useField } from 'vee-validate'

// TODO: test & implement the following props: "veeId", "required"

const props = defineProps({
  /**
   * from vee-validate example at https://vee-validate.logaretm.com/v4/examples/custom-inputs/
   */

  modelValue: {
    type: String,
    default: undefined,
  },
  successMessage: {
    type: String,
    default: '',
  },

  /**
   * props from formComponentPropsMixin
   */
  id: {
    type: String,
    required: false,
    default: null,
  },

  // vuetify property hideDetails
  filled: {
    type: Boolean,
    default: true,
  },

  // vuetify property hideDetails
  hideDetails: {
    type: String,
    default: 'auto',
  },

  // set classes on input
  inputClass: {
    type: String,
    default: '',
    required: false,
  },

  // used as field name for validation and as label (if no override label is provided)
  name: {
    type: String,
    required: false,
    default: null,
  },

  // override the label which is displayed to the user; name is used instead if no label is provided
  label: {
    type: String,
    required: false,
    default: null,
  },

  // error messages from outside which should be displayed on the component
  errorMessages: {
    type: Array,
    required: false,
    default: () => [],
  },

  /**
   * props from formComponentMixin.js
   */

  // ID for vee-validation
  veeId: {
    type: String,
    required: false,
    default: null,
  },

  // rules for vee-validation
  veeRules: {
    type: [String, Object],
    required: false,
    default: '',
  },

  /**
   * additional props for ETextField
   */
  type: {
    type: String,
    default: 'text',
  },
})

const name = toRef(props, 'name')
const rules = toRef(props, 'veeRules')
const {
  value: inputValue,
  errorMessage,
  errors,
  handleBlur,
  handleChange,
  meta,
} = useField(name, rules, {
  initialValue: props.modelValue,
})
</script>

<!-- <script>
import { Field } from 'vee-validate'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'

export default {
  name: 'ETextField',
  components: {
    Field,
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
            vm.$emit('input', value)
          },
          blur: function () {
            vm.$emit('blur')
            if (vm.$data.preventValidationOnBlur) {
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
      'autofocus' in this.$attrs &&
      /*'required' in this.$refs.validationProvider.$attrs && */
      this.$refs.textField.value == ''
  },
  methods: {
    focus() {
      this.$refs.textField.focus()
    },
    isRequired(value) {
      if (value && value.trim()) {
        return true
      }
      return 'This is required'
    },
  },
}
</script> -->

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
