<!--
Displays a field as text or as an input field, depending on the editing prop.
You can two-way bind to the value using v-model.
-->

<template>
  <span>
    <span v-if="!editing">{{ value }}</span>
    <span v-if="editing">
      <v-form
        inline
        class="mb-2">
        <slot :editing="editing"
              :localValue="localValue"
              :errorMessage="errorMessage"
              :isSaving="isSaving"
              :status="status"
              :on="eventHandlers" />

        <v-btn
          v-if="!autoSave"
          small
          color="warning"
          class="mb-1"
          @click="reset">
          Reset
        </v-btn>

        <v-btn
          v-if="!autoSave"
          color="primary"
          small
          :disabled="isSaving || (required && $v.localValue.$invalid)"
          class="mr-2 ml-2 mb-1"
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
      </v-form>
    </span>
  </span>
</template>

<script>

import { debounce } from 'lodash'
import { validationMixin } from 'vuelidate'
import { required } from 'vuelidate/lib/validators'
import { apiPropsMixin } from '@/mixins/apiPropsMixin'

export default {
  name: 'ApiWrapper',
  mixins: [apiPropsMixin, validationMixin],
  data () {
    return {
      localValue: this.value,
      isSaving: false,
      showSuccessIcon: false,
      dirty: false,
      eventHandlers: {
        save: this.save,
        reset: this.reset,
        input: this.onInput,
        touch: this.touch
      }
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
    },
    status: function () {
      if (this.isSaving) {
        return 'saving'
      } else if (this.showSuccessIcon) {
        return 'success'
      } else {
        return 'init'
      }
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

    log: function (title, event) {
      console.log(title)
      console.log(event)
    },
    touch: function () {
      this.$v.localValue.$touch()
    },
    onInput: function (newValue) {
      this.localValue = newValue
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
        setTimeout(() => { this.showSuccessIcon = false }, 2000)
      }, (e, a) => {
        console.log('error')
      })
    }
  }

}
</script>

<style scoped>
</style>
