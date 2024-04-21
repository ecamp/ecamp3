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
      <!-- passing through all slots -->
      <slot v-for="(_, name) in $slots" :slot="name" :name="name" />
      <template v-for="(_, name) in $scopedSlots" :slot="name" slot-scope="slotData">
        <slot :name="name" v-bind="slotData" />
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
     * Format internal value for display in the UI
     */
    format: { type: Function, required: false, default: null },

    /**
     * Parse a user-supplied value into the internal format
     */
    parse: { type: Function, required: false, default: null },

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
       * valid value (parsed type or null)
       */
      localValue: null,

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
        if (val === this.localValue) {
          return
        }
        this.localValue = val
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
      }
      if (this.debouncedParse) {
        this.debounceParseValue(value)
      } else {
        this.parseValue(value)
      }
    },
    onBlur(event) {
      if (this.resetOnBlur) {
        this.stringValue = this.format?.(this.localValue) ?? this.localValue
      }
      this.$emit('blur', event)
    },
    setValue(val) {
      if (
        (this.parse?.(this.localValue) ?? this.localValue) !== (this.parse?.(val) ?? val)
      ) {
        this.localValue = val

        this.$emit('input', val)
      }
    },
    async parseValue(val) {
      try {
        if (this.parse != null) {
          val = await this.parse(val)
        }
        this.setValue(val)
        // after saving value, trigger validations
        this.$refs.validationProvider.validate(this.localValue)
      } catch (error) {
        this.parseError = error
      }
    },
  },
}
</script>
