<!--
Displays a field as text or as an input field, depending on the editing prop.
You can two-way bind to the value using v-model.
-->

<template>
  <span>
    <span v-if="!editing">{{ fieldname }}: {{ value }}</span>
    <span v-if="editing">{{ fieldname }}:
      <input
        v-model="valueModel"
        class="form-control">
    </span>
  </span>
</template>

<script>
export default {
  name: 'ToggleableInput',
  props: {
    editing: { type: Boolean, default: false },
    fieldname: { type: String, default: '' },
    value: { type: String, required: true }
  },
  computed: {
    valueModel: {
      get () {
        return this.value
      },
      set (newValue) {
        this.$emit('input', newValue)
      }
    }
  }
}
</script>

<style scoped>

</style>
