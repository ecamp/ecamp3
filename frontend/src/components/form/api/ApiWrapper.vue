<!--
Wrapper component for form components to save data back to API
-->

<template>
  <v-form
    :class="['d-flex','flex-wrap',{'api-wrapper--inline':!autoSave && !readonly && !separateButtons, 'my-4':!noMargin}]"
    @submit.prevent="onEnter">
    <slot
      :localValue="localValue"
      :label="label"
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
        type="submit"
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
import { apiPropsMixin } from '@/mixins/apiPropsMixin'
import { validate } from 'vee-validate'

export default {
  name: 'ApiWrapper',
  mixins: [apiPropsMixin],
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
      hasValidationError: false,
      hasServerError: false,
      hasLoadingError: false,
      serverErrorMessage: '',
      loadingErrorMessage: '',
      validationErrorMessages: [],
      eventHandlers: {
        save: this.save,
        reset: this.reset,
        reload: this.reload,
        input: this.onInput
      }
    }
  },
  computed: {
    hasFinishedLoading () {
      return !this.isLoading && !this.hasLoadingError
    },
    errorMessages () {
      const errors = []

      // 1st priority: Loading error
      if (this.hasLoadingError) errors.push(this.loadingErrorMessage)

      // 2nd priority: Frontent validation
      if (this.hasValidationError) this.validationErrorMessages.forEach(err => errors.push(err))

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
    async onInput (newValue) {
      this.localValue = newValue
      this.dirty = true

      if (this.required || this.validation) {
        const rules = []
        if (this.required) rules.push('required')
        if (this.validation) rules.push(this.validation)

        const result = await validate(newValue, rules.join('|'), {
          name: this.label
        })

        this.hasValidationError = !result.valid
        this.validationErrorMessages = result.errors
      }

      if (this.autoSave) {
        this.debouncedSave()
      }
    },
    // reload data from API (doesn't force loading from server if available locally)
    reload () {
      this.isLoading = true
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
      this.hasLoadingError = false
      this.hasValidationError = false
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
      if (this.hasValidationError) {
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
