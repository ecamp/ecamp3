<template>
  <span class="category-label" :style="style()">{{ category.short }}</span>
</template>

<script>
import { contrastColor } from '@/../common/helpers/colors.js'
import Color from 'colorjs.io'

export default {
  props: {
    category: { type: Object, required: true },
  },
  async fetch() {
    await Promise.all([this.category._meta.load])
  },
  methods: {
    style() {
      return {
        backgroundColor: this.category.color,
        color: contrastColor(new Color(this.category.color)).toString({ format: 'hex' }),
      }
    },
  },
}
</script>

<style lang="scss">
.category-label {
  padding: 0.1rem 0.6rem;
  border-radius: 999px;
}
</style>
