<template>
  <div class="e-form-container">
    <slot />
  </div>
</template>

<script>

export default {
  name: 'ApiForm',
  props: {
    entity: { required: true, type: Object }
  },
  provide () {
    return {
      apiUri: this.entity._meta.self
    }
  }
}
</script>
