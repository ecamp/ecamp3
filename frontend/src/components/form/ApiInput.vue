<!--
Displays a field as text or as an input field, depending on the editing prop.
You can two-way bind to the value using v-model.
-->

<template>
  <div class="mb-4">
    <v-text-field v-if="!editing" :label="label"
                  hide-details="auto"
                  v-model="value" readonly />
    <v-text-field
      v-if="editing"
      class="api-input"
      v-model="localValue"
      :label="label"
      name="api-input"
      hide-details="auto"
      :error-messages="errorMessage"
      :state="required && $v.localValue.$dirty ? !$v.localValue.$error : null"
      v-bind="$attrs"
      required
      @input="onInput"
      @blur="$v.localValue.$touch()">

      <template slot="append-outer">
        <v-btn
          v-if="!autoSave"
          small
          color="warning"
          class="mb-0"
          @click="reset">

          Reset
        </v-btn>

        <v-btn
          small color="primary"
          :disabled="isSaving || (required && $v.localValue.$invalid)"
          class="mb-0"
          @click="save">

          <v-progress-circular
            v-if="isSaving"
            indeterminate
            color="primary"
            size="20"
            class="mr-2" />

          <v-icon
            v-if="showSuccessIcon"
            left>
            mdi-check
          </v-icon>

          Save
        </v-btn>
      </template>
    </v-text-field>
  </div>
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

    /* overrideDirty=true will reset the input if 'value' changes, even if the input is dirty. Use with caution. */
    overrideDirty: { type: Boolean, default: false, required: false },

    /* enable/disable edit mode */
    editing: { type: Boolean, default: true, required: false },

    /* enable/disable auto save */
    autoSave: { type: Boolean, default: true, required: false },
    autoSaveDelay: { type: Number, default: 800, required: false },

    /* Validation criteria */
    required: { type: Boolean, default: false, required: false }
  },
  data () {
    return {
      localValue: this.value,
      isSaving: false,
      showSuccessIcon: false,
      dirty: false
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
    },
    errorMessage () {
      const errors = []
      if (!this.$v.localValue.$dirty) return errors
      !this.$v.localValue.required && errors.push('Feld darf nicht leer sein.')
      return errors
    }
  },
  watch: {
    value: function (newValue, oldValue) {
      // override local value if it wasn't dirty
      if (!this.dirty || this.overrideDirty) {
        this.localValue = newValue
      }

      // clear dirty if outside value changes to same as local value (e.g. after save operation)
      if (this.localValue === newValue) {
        this.dirty = false
      }
    }
  },
  created: function () {
    // Create debounced save method (lodash debounce function)
    this.debouncedSave = debounce(this.save, this.autoSaveDelay)
  },
  methods: {
    onInput: function () {
      this.dirty = true
      this.$v.localValue.$touch()

      if (this.autoSave) {
        this.debouncedSave()
      }
    },
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
        setTimeout(() => {
          this.showSuccessIcon = false
        }, 2000)
      })
    }
  }
}
</script>

<style scoped>
  .dirty {
    border: 1px red solid;
  }
  .api-input ::v-deep .v-input__append-outer {
    margin-bottom: 0;
  }
</style>
