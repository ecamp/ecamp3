<!--
Displays a field as a textfield (can be used with v-model)
-->

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
      :value="stringValue"
      :filled="filled"
      :required="required"
      :hide-details="hideDetails"
      :error-messages="veeErrors.concat(combinedErrorMessages)"
      :label="labelOrEntityFieldLabel"
      :class="[inputClass]"
      type="text"
      v-on="inputListeners"
    >
      <template #prepend>
        <slot
          name="prepend"
          :string-value="stringValue"
          :internal-value="internalValue"
          :serialized-value="serializedValue"
        />
      </template>
      <template #append>
        <slot
          name="append"
          :string-value="stringValue"
          :internal-value="internalValue"
          :serialized-value="serializedValue"
        />
      </template>
    </v-text-field>
  </ValidationProvider>
</template>

<script>
import { debounce } from 'lodash'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { ValidationProvider } from 'vee-validate'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'

export default {
  name: 'EParseField',
  components: { ValidationProvider },
  mixins: [formComponentPropsMixin, formComponentMixin],
  props: {
    value: { validator: () => true, required: true },
    readonly: { type: Boolean, required: false, default: false },
    disabled: { type: Boolean, required: false, default: false },
    errorMessages: { type: Array, required: false, default: () => [] },

    /**
     * Debounce the parse function to avoid unnecessary calls and invalid values
     */
    debouncedParse: { type: Boolean, default: false },

    resetOnBlur: { type: Boolean, default: false },

    /**
     * Format internal value to string
     */
    format: { type: Function, required: false, default: null },

    /**
     * Parse a user-supplied value into the internal value type
     */
    parse: { type: Function, required: false, default: null },

    /**
     * Serialize the internal value for output
     */
    serialize: { type: Function, required: false, default: null },

    /**
     * Deserialize the serialized value into the internal value type
     */
    deserialize: { type: Function, required: false, default: null },

    /**
     * Override the default serialized values comparison function
     */
    compare: { type: Function, required: false, default: (a, b) => a === b },

    /**
     * Remove unwanted characters from the input value
     */
    inputFilter: { type: Function, required: false, default: null },
  },
  data() {
    return {
      /**
       * string displayed in the text field
       */
      stringValue: null,

      /**
       * parsed value (parsed type or null)
       */
      internalValue: null,

      /**
       * serialized value (for output)
       */
      serializedValue: null,

      intermediateParseError: null,
      parseError: null,

      /**
       * note that it is necessary to debounce in data to have one debounced function per instance, whereas
       * debouncing in watch or methods results in one global debounced function which has unwanted effects
       * when there are multiple instances rendered at the same time
       */
      debounceParseValue: debounce(this.parseValue, 500),
    }
  },
  computed: {
    combinedErrorMessages() {
      if (this.parseError == null) {
        return this.errorMessages
      }
      return [...this.errorMessages, this.parseError.message]
    },
    inputListeners: function () {
      const vm = this
      return Object.assign(
        {},
        // attach all $parent listeners
        this.$listeners,
        // override @input listener for correct handling of numeric values
        {
          input: vm.onInput,
          blur: vm.onBlur,
        }
      )
    },
  },
  watch: {
    value: {
      handler(val) {
        // if the value is the same, we don't need to parse it again
        this.parseError = null
        this.intermediateParseError = null
        if (this.compare(val, this.serializedValue)) {
          return
        }
        this.serializedValue = val
        this.internalValue = this.deserialize?.(val) ?? val
        this.stringValue = this.format?.(val) ?? val
      },
      immediate: true,
    },
  },
  methods: {
    onInput(value) {
      if (this.inputFilter) {
        value = this.inputFilter(value)
        this.stringValue = value
        this.$refs.textField.lazyValue = value
      } else {
        this.stringValue = value
      }
      if (this.debouncedParse) {
        this.debounceParseValue(value)
      } else {
        this.parseValue(value)
      }
    },
    onBlur(event) {
      if (this.resetOnBlur && !this.intermediateParseError) {
        this.stringValue = this.format?.(this.internalValue) ?? this.internalValue
        this.$refs.textField.lazyValue = this.stringValue
      }
      this.parseError = this.intermediateParseError
      this.$refs.validationProvider.validate(this.serializedValue)
      this.$emit('blur', event)
    },
    setValue(val) {
      if (!this.compare(this.serializedValue, this.serialize?.(val) ?? val)) {
        this.internalValue = val

        this.serializedValue = this.serialize?.(val) ?? val
        this.$emit('input', this.serialize?.(val) ?? val)
      }
    },
    async parseValue(val) {
      try {
        if (this.parse != null) {
          val = await this.parse(val)
        }
        this.setValue(val)
        this.parseError = null
        this.intermediateParseError = null
      } catch (error) {
        this.intermediateParseError = error
      }
    },
    focus() {
      this.$refs.textField.focus()
    },
  },
}
</script>
