<template>
  <div class="e-form-container">
    <slot />
  </div>
</template>

<script>
export default {
  name: 'ApiForm',
  provide () {
    return {
      apiUri: this.entity._meta.self
    }
  },
  props: {
    entity: { required: true, type: Object }
  }
}
</script>
