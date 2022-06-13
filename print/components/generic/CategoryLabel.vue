<template>
  <span
    class="category-label"
    :style="{ 'background-color': category.color }"
    >{{ category.short }}</span
  >
</template>

<script>
export default {
  props: {
    category: { type: Object, required: true },
  },
  async fetch() {
    await Promise.all([this.category._meta.load])
  },
}
</script>

<style lang="scss">
.category-label {
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
}
</style>
