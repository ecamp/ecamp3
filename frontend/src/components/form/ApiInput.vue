<!--
Displays a field as text or as an input field, depending on the editing prop.
You can two-way bind to the value using v-model.
-->

<template>
  <span>
    <span v-if="!editing">{{ value }}</span>
    <span v-if="editing">
      <input
        id="input"
        v-model="localValue"
        class="form-control"
        :class="{ dirty: isDirty }">

      <button
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
        Save
      </button>

    </span>
  </span>
</template>

<script>
export default {
  name: 'ApiInput',
  props: {
    value: { type: String, required: true },

    /* field name and URI for saving back to API */
    fieldname: { type: String, required: true },
    uri: { type: String, required: true },

    /* overrideDirty=true will reset the input if 'value' changes, even if the input is dirty */
    overrideDirty: { type: Boolean, default: false, required: false },

    /* enable/disable edit mode */
    editing: { type: Boolean, default: true, required: false }
  },
  data () {
    return {
      localValue: this.value,
      isSaving: false
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
    }
  },
  methods: {
    reset: function (event) {
      this.localValue = this.value
    },
    save: function (event) {
      this.isSaving = true
      this.api.patch(this.uri, { [this.fieldname]: this.localValue }).then(() => {
        this.isSaving = false
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
