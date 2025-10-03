<template>
  <div class="e-form-container">
    <slot />
  </div>
</template>

<script>
export default {
  name: 'ApiForm',
  provide() {
    return {
      apiUri: this.entity._meta.self,
      entityName: this.name,
    }
  },
  props: {
    entity: { required: true, type: Object },
    name: { required: false, type: String, default: null },
  },
}
</script>
