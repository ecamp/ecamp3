<!--
Displays a field as text or as an input field, depending on the editing prop.
You can two-way bind to the value using v-model.
-->

<template>
  <span>
    <span v-if="!editing">{{ value }}</span>
    <span v-if="editing">
      <div class="input-group">
        <input
          v-model="localValue"
          v-bind="$attrs"
          class="form-control"
          :class="{ dirty: isDirty }">

        <div class="input-group-append">

          <button
            v-if="!autoSave"
            class="btn btn-primary"
            type="button"
            @click="reset">

            Reset
          </button>

          <button
            class="btn btn-primary"
            type="button"
            :disabled="isSaving"
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
          </button>

        </div>
      </div>

    </span>
  </span>
</template>

<script>
import { debounce } from 'lodash'

export default {
  name: 'ApiInput',
  inheritAttrs: false,
  props: {
    value: { type: String, required: true },

    /* field name and URI for saving back to API */
    fieldname: { type: String, required: true },
    uri: { type: String, required: true },

    /* overrideDirty=true will reset the input if 'value' changes, even if the input is dirty */
    overrideDirty: { type: Boolean, default: false, required: false },

    /* enable/disable edit mode */
    editing: { type: Boolean, default: true, required: false },

    /* enable/disable auto save */
    autoSave: { type: Boolean, default: true, required: false }
  },
  data () {
    return {
      localValue: this.value,
      isSaving: false,
      showSuccessIcon: false
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
    },
    save: function (event) {
      this.isSaving = true
      this.api.patch(this.uri, { [this.fieldname]: this.localValue }).then(() => {
        this.isSaving = false

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
