<template>
  <v-chip
    v-if="!category()._meta.loading"
    :color="category().color"
    class="e-category-chip tabular-nums"
    dark
    small>
    {{ category().short }}
  </v-chip>
</template>
<script>
export default {
  name: 'CategoryChip',
  props: {
    category: {
      type: Function,
      required: true
    }
  }
}
</script>

<style scoped>
.e-category-chip {
  height: 20px;
  padding-left: 7px;
  padding-right: 7px;
  min-width: 28px;
  justify-content: center;
}
</style>
