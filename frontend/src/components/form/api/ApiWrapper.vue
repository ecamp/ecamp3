<!--
Wrapper component for form components to save data back to API
-->

<template>
  <ValidationObserver ref="validationObserver" v-slot="validationObserver" slim>
    <v-form
      :class="[{'api-wrapper--inline':!autoSave && !readonly && !separateButtons}]"
      class="e-form-container"
      @submit.prevent="onEnter">
      <slot
        :localValue="localValue"
        :hasServerError="hasServerError"
        :hasLoadingError="hasLoadingError"
        :hasValidationError="validationObserver.invalid"
        :errorMessages="errorMessages"
        :isSaving="isSaving"
        :isLoading="isLoading"
        :autoSave="autoSave"
        :readonly="readonly || !hasFinishedLoading"
        :status="status"
        :dirty="dirty"
        :on="eventHandlers" />
    </v-form>
  </ValidationObserver>
</template>

<script>

import { debounce, set, get } from 'lodash'
import { apiPropsMixin } from '@/mixins/apiPropsMixin.js'
import { ValidationObserver } from 'vee-validate'
import serverErrorToString from '@/helpers/serverErrorToString.js'

export default {
  name: 'ApiWrapper',
  components: { ValidationObserver },
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
      isMounted: false,
      showIconSuccess: false,
      dirty: false,
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
      if (this.hasLoadingError) errors.push(this.loadingErrorMessage)
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

      // while loading, value is null
      } else if (this.isLoading) {
        return null

      // avoid infinite reloading if loading from API has failed
      } else if (this.hasLoadingError) {
        return null

      // return value from API unless `value` is set explicitly
      } else {
        const resource = this.api.get(this.uri)
        let val = get(resource, this.fieldname)

        // resource is loaded, but val is still undefined (=doesn't exist)
        if (val === undefined) {
          console.error('You are trying to use a fieldname ' + this.fieldname + ' in an ApiFormComponent, but ' + this.fieldname + ' doesn\'t exist on entity ' + this.uri)
          return null
        }

        // while loading, value is null
        // (necessary because while loading, even normal properties are returned as functions)
        if (resource._meta.loading || val?._meta?.loading) return null

        // Check if val is an (embedded) relation
        if (val instanceof Function) {
          val = val()
          if (!('items' in val)) {
            return val._meta.self // val is an embedded relation (*ToOne) --> return IRI
          } else {
            return val.items.map(item => item._meta.self) // val is an embedded collection (*ToMany) --> return array of IRIs
          }
        }

        // standard case: value is a primitive value
        return val
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
  mounted () {
    this.isMounted = true
  },
  methods: {
    async onInput (newValue) {
      this.localValue = newValue
      this.dirty = this.localValue !== this.apiValue

      if (this.autoSave) {
        this.debouncedSave()
      }
    },
    // reload data from API (doesn't force loading from server if available locally)
    reload () {
      this.resetErrors()
      const obj = this.api.get(this.uri)
      this.isLoading = obj._meta.loading

      // initial data load from API
      obj._meta.load.then(() => {
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
      this.$emit('reseted')
      this.$emit('finished')
    },
    resetErrors () {
      this.dirty = false
      this.hasLoadingError = false
      this.hasServerError = false
      this.serverErrorMessage = null
      if (this.isMounted) { this.$refs.validationObserver.reset() }
    },
    onEnter () {
      if (!this.autoSave) {
        this.save()
      }
    },
    async save () {
      // abort saving if component is in readonly or disabled state
      // this is here for safety reasons, should not be triggered if the wrapped component behaves normally
      if (this.readonly || this.disabled) {
        return
      }

      // abort saving in case of validation errors
      const isValid = await this.$refs.validationObserver.validate()
      if (!isValid) {
        return
      }

      // reset all dirty flags and start saving
      this.resetErrors()
      this.isSaving = true

      // construct payload (nested path allowed)
      const payload = {}
      set(payload, this.fieldname, this.localValue)

      this.api.patch(this.uri, payload).then(() => {
        this.isSaving = false
        this.showIconSuccess = true
        this.$emit('saved')
        this.$emit('finished')
        setTimeout(() => { this.showIconSuccess = false }, 2000)
      }, (error) => {
        this.isSaving = false
        this.serverErrorMessage = serverErrorToString(error, this.fieldname)
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
