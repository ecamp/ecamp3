<!--
Wrapper component for form components to save data back to API
-->

<template>
  <v-form
    :class="['d-flex','flex-wrap',{'api-wrapper--inline':!autoSave && !readonly && !separateButtons, 'my-4':!noMargin}]"
    @submit.prevent="onEnter">
    <slot
      :localValue="localValue"
      :hasServerError="hasServerError"
      :hasLoadingError="hasLoadingError"
      :hasValidationError="hasValidationError"
      :errorMessages="errorMessages"
      :isSaving="isSaving"
      :isLoading="isLoading"
      :autoSave="autoSave"
      :readonly="readonly || !hasFinishedLoading"
      :status="status"
      :on="eventHandlers" />

    <div
      v-if="!autoSave && !readonly"
      :class="['d-flex', {'my-1 ml-auto': separateButtons}]">
      <v-btn
        :disabled="disabled || !hasFinishedLoading"
        small
        elevation="0"
        :class="{'mr-1': separateButtons}"
        :tile="!separateButtons"
        :height="separateButtons ? '' : 'auto'"
        @click="reset">
        Reset
      </v-btn>

      <v-btn
        :color="hasServerError ? 'error' : 'primary'"
        small
        elevation="0"
        :disabled="disabled || !hasFinishedLoading || hasValidationError"
        class="v-btn--last-instance"
        :height="separateButtons ? '' : 'auto'"
        :loading="isSaving"
        @click="save">
        {{ hasServerError ? 'Retry' : 'Save' }}
      </v-btn>
    </div>
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
  props: {
    separateButtons: {
      type: Boolean,
      default: true
    }
  },
  data () {
    return {
      localValue: null,
      isSaving: false,
      isLoading: false,
      showIconSuccess: false,
      dirty: false,
      hasServerError: false,
      hasLoadingError: false,
      serverErrorMessage: null,
      loadingErrorMessage: null,
      eventHandlers: {
        save: this.save,
        reset: this.reset,
        reload: this.reload,
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
    hasFinishedLoading () {
      return !this.isLoading && !this.hasLoadingError
    },
    hasValidationError () {
      return this.$v.localValue.$invalid
    },
    errorMessages () {
      const errors = []

      // 1st priority: Loading error
      if (this.hasLoadingError) errors.push(this.loadingErrorMessage)

      // 2nd priority: Frontent validation
      if (this.$v.localValue.$dirty) !this.$v.localValue.required && errors.push('Feld darf nicht leer sein.')

      // 3rd priority: Server error (not displayed in case of frontend validation error)
      if (this.hasServerError) errors.push(this.serverErrorMessage)

      return errors
    },
    status: function () {
      if (this.isSaving) {
        return 'saving'
      } else if (this.showIconSuccess) {
        return 'success'
      } else {
        return 'init'
      }
    },
    debouncedSave () {
      return debounce(this.save, this.autoSaveDelay)
    },
    apiValue () {
      // return value from props if set explicitly
      if (this.value) {
        return this.value

      // avoid infinite reloading if loading from API has failed
      } else if (this.hasLoadingError) {
        return null

      // return value from API unless `value` is set explicitly
      } else {
        return this.api.get(this.uri)[this.fieldname]
      }
    }
  },
  watch: {
    apiValue: function (newValue, oldValue) {
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
  created () {
    // initial data load from API
    if (!this.value) this.reload()

    this.localValue = this.apiValue
  },
  methods: {
    touch () {
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
    // reload data from API (doesn't force loading from server if available locally)
    reload () {
      this.isLoading = true
      this.hasLoadingError = false
      this.resetErrors()

      // initial data load from API
      this.api.get(this.uri)._meta.load.then(() => {
        this.isLoading = false
      }).catch(error => {
        this.isLoading = false
        this.hasLoadingError = true
        this.loadingErrorMessage = error.message
      })
    },
    reset () {
      this.localValue = this.apiValue
      this.resetErrors()
    },
    resetErrors () {
      this.dirty = false
      this.$v.localValue.$reset()
      this.hasServerError = false
      this.serverErrorMessage = null
    },
    onEnter () {
      if (!this.autoSave) {
        this.save()
      }
    },
    save () {
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
      this.resetErrors()
      this.isSaving = true

      this.api.patch(this.uri, { [this.fieldname]: this.localValue }).then(() => {
        this.isSaving = false
        this.showIconSuccess = true
        setTimeout(() => { this.showIconSuccess = false }, 2000)
      }, (error) => {
        this.isSaving = false

        // 422 validation error
        if (error.name === 'ServerException' && error.response && error.response.status === 422) {
          this.serverErrorMessage = 'Validation error: '
          const validationMessages = error.response.data.validation_messages[this.fieldname]
          Object.keys(validationMessages).forEach((key) => {
            this.serverErrorMessage = this.serverErrorMessage + validationMessages[key] + '. '
          })
        } else {
          this.serverErrorMessage = error.message
        }

        this.hasServerError = true
      })
    }
  }

}
</script>

<style lang="scss" scoped>
  .api-wrapper--inline .v-btn--last-instance {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }
  .api-wrapper--inline .v-btn {
    border-top: 1px solid rgba(0, 0, 0, 0.38);
    border-bottom: 1px solid rgba(0, 0, 0, 0.38);
  }
</style>

<style lang="scss" >
  .api-wrapper--inline .v-text-field {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
  }
</style>
