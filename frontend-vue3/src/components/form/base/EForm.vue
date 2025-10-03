<template>
  <div>
    <slot />
  </div>
</template>

<script>
export default {
  name: 'EForm',
  provide() {
    return {
      entityName: this.name,
    }
  },
  props: {
    name: { required: false, type: String, default: null },
  },
}
</script>
