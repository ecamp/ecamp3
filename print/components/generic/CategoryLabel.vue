<template>
  <span class="tw-inline-flex tw-items-center">
    <span class="tw-select-none">&#8203;</span>
    <span class="category-label tw-tabular-nums" :style="style()">{{
      category.short
    }}</span>
  </span>
</template>

<script setup>
const props = defineProps({
  category: { type: Object, required: true },
})

await useAsyncData(`CategoryLabel-${props.category._meta.self}`, async () => {
  await Promise.all([props.category._meta.load])
})
</script>

<script>
import { contrastColor } from '@/../common/helpers/colors.js'
export default {
  methods: {
    style() {
      return {
        backgroundColor: this.category.color,
        color: contrastColor(this.category.color),
      }
    },
  },
}
</script>

<style lang="scss" scoped>
.category-label {
  font-size: 80%;
  letter-spacing: 0.05em;
  padding: 0.35em 0.65em;
  line-height: 80%;
  border-radius: 999px;
}
</style>
