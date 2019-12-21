<!--
Displays a field as text or as an input field, depending on the editing prop.
You can two-way bind to the value using v-model.
-->

<template>
  <span>
    <span v-if="!editing">{{ value }}</span>
    <span v-if="editing">
      <b-form
        inline
        class="mb-2">

        <b-form-group
          id="api-input-group"
          :label="label"
          label-for="api-input"

          invalid-feedback="Dieses Feld kann nicht leer sein.">

          <b-form-input
            id="api-input"
            v-model="$v.localValue.$model"
            name="api-input"
            class="mr-2 ml-2"
            :state="required && $v.localValue.$dirty ? !$v.localValue.$error : null"
            v-bind="$attrs" />

        </b-form-group>

        <b-button
          v-if="!autoSave"
          variant="primary"
          @click="reset">

          Reset
        </b-button>

        <b-button
          variant="primary"
          :disabled="isSaving || (required && $v.localValue.$invalid)"
          class="mr-2 ml-2"
          @click="save">

          <span
            v-if="isSaving"
            class="spinner-border spinner-border-sm"
            role="status"
            aria-hidden="true" />

          <i
            v-if="showSuccessIcon"
            class="zmdi zmdi-check" />

          Save
        </b-button>
      </b-form>
    </span>
  </span>
</template>

<script>
import { debounce } from 'lodash'
import { validationMixin } from 'vuelidate'
import { required } from 'vuelidate/lib/validators'

export default {
  name: 'ApiInput',
  mixins: [validationMixin],
  inheritAttrs: false,
  props: {
    value: { type: String, required: true },

    /* field name and URI for saving back to API */
    fieldname: { type: String, required: true },
    uri: { type: String, required: true },

    /* display label */
    label: { type: String, default: '', required: false },

    /* overrideDirty=true will reset the input if 'value' changes, even if the input is dirty */
    overrideDirty: { type: Boolean, default: false, required: false },

    /* enable/disable edit mode */
    editing: { type: Boolean, default: true, required: false },

    /* enable/disable auto save */
    autoSave: { type: Boolean, default: true, required: false },

    /* Validation criteria */
    required: { type: Boolean, default: false, required: false }
  },
  data () {
    return {
      localValue: this.value,
      isSaving: false,
      showSuccessIcon: false
    }
  },
  validations: {
    localValue: {
      required
    }
  },
  computed: {
    isDirty: function () {
      return this.value !== this.localValue
    }
  },
  watch: {
    value: function (newValue, oldValue) {
      // override local value if it wasn't dirty
      if (this.localValue === oldValue || this.overrideDirty) {
        this.localValue = newValue
      }
    },
    localValue: function (newValue, oldValue) {
      if (this.autoSave) {
        this.debouncedSave()
      }
    }
  },
  created: function () {
    // Create debounced save method (lodash debounce function)
    this.debouncedSave = debounce(this.save, 500)
  },
  methods: {
    reset: function (event) {
      this.localValue = this.value
      this.$v.localValue.$reset()
    },
    save: function (event) {
      this.$v.localValue.$touch()
      if (this.required && this.$v.localValue.$anyError) {
        return
      }

      this.isSaving = true
      this.api.patch(this.uri, { [this.fieldname]: this.localValue }).then(() => {
        this.isSaving = false
        this.$v.localValue.$reset()

        this.showSuccessIcon = true
        setTimeout(() => { this.showSuccessIcon = false }, 2000)
      })
    }
  }
}
</script>

<style scoped>
  .dirty{
    border:1px red solid;
  }
</style>
