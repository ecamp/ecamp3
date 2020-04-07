<!--
Wrapper component for form components to save data back to API
-->

<template>
  <v-form
    :class="['d-flex','flex-wrap',{'api-wrapper--inline':!autoSave && !readonly && !separateButtons, 'my-4':!noMargin}]"
    @submit.prevent="onEnter">
    <slot
      :localValue="localValue"
      :errorMessages="errorMessages"
      :isSaving="isSaving"
      :status="status"
      :on="eventHandlers" />

    <div
      v-if="!autoSave && !readonly"
      :class="['d-flex', {'my-1 ml-auto': separateButtons}]">
      <v-btn
        :disabled="disabled"
        small
        elevation="0"
        :class="{'mr-1': separateButtons}"
        :tile="!separateButtons"
        :height="separateButtons ? '' : 'auto'"
        @click="reset">
        Reset
      </v-btn>

      <v-btn
        color="primary"
        small
        elevation="0"
        :disabled="disabled || (required && $v.localValue.$invalid)"
        class="v-btn--last-instance"
        :height="separateButtons ? '' : 'auto'"
        :loading="isSaving"
        @click="save">
        Save
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
      default: false
    }
  },
  data () {
    return {
      localValue: null,
      isSaving: false,
      showIconSuccess: false,
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
      return this.apiValue !== this.localValue
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
      // return value from API unless `value` is set explicitly
      return this.value || this.api.get(this.uri)[this.fieldname]
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
    reset () {
      this.localValue = this.apiValue
      this.$v.localValue.$reset()
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
      this.dirty = false
      this.$v.localValue.$reset()
      this.isSaving = true

      this.api.patch(this.uri, { [this.fieldname]: this.localValue }).then(() => {
        this.isSaving = false
        this.showIconSuccess = true
        setTimeout(() => {
          this.showIconSuccess = false
        }, 2000)
      }, (e, a) => {
        console.log('error')
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
