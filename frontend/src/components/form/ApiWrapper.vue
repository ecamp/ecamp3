<!--
Wrapper component for form components to save data back to API
-->

<template>
  <v-form
    inline
    :class="{'d-flex api-wrapper--savable':!autoSave && !readonly,'my-4':!noMargin}">
    <slot
      :localValue="localValue"
      :errorMessages="errorMessages"
      :isSaving="isSaving"
      :status="status"
      :on="eventHandlers" />

    <v-btn
      v-if="!autoSave && !readonly"
      :disabled="disabled"
      small elevation="0"
      color="warning" tile
      class="mb-0"
      height="auto"
      @click="reset">
      Reset
    </v-btn>

    <v-btn
      v-if="!autoSave && !readonly"
      color="primary"
      small elevation="0"
      :disabled="disabled || isSaving || (required && $v.localValue.$invalid)"
      class="mb-0 v-btn--last-instance"
      height="auto"
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
    errorMessages () {
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
    },
    debouncedSave () {
      return debounce(this.save, this.autoSaveDelay)
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
  methods: {
    touch: function () {
      this.$v.localValue.$touch()
    },
    onInput: function (newValue) {
      this.localValue = newValue
      this.dirty = true
      this.touch()

      if (this.autoSave) {
        this.debouncedSave()
      }
    },
    reset: function (event) {
      this.localValue = this.value
      this.$v.localValue.$reset()
    },
    save: function (event) {
      // abort saving if component is in readonly or disabled state
      // this is here for safety reasons, should not be triggered if the wrapped component behaves normally
      if (this.readonly || this.disabled) {
        return
      }

      // abort saving in case of validation errors
      this.touch()
      if (this.$v.localValue.$anyError) {
        return
      }

      // reset all dirty flags and start saving
      this.dirty = false
      this.$v.localValue.$reset()
      this.isSaving = true

      this.api.patch(this.uri, { [this.fieldname]: this.localValue }).then(() => {
        this.isSaving = false
        this.showSuccessIcon = true
        setTimeout(() => {
          this.showSuccessIcon = false
        }, 2000)
      }, (e, a) => {
        console.log('error')
      })
    }
  }

}
</script>

<style lang="scss" scoped>
  .v-btn--last-instance {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }
</style>

<style lang="scss" >
  .api-wrapper--savable .v-text-field {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
  }
</style>
