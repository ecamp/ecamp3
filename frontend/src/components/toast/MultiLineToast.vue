<template>
  <span v-if="lines.length === 0">
    {{ generalErrorText }}
  </span>
  <span v-else>
    <ul>
      <li v-for="(line, index) in lines.slice(0, 3)" :key="index">
        {{ line }}
      </li>
      <li v-if="lines.length > 3">...</li>
    </ul>
  </span>
</template>

<script>
export default {
  name: 'MultiLineToast',
  props: {
    lines: {
      type: Array,
      required: true,
    },
    generalErrorText: {
      type: String,
      required: true,
    },
  },
}
</script>

<style scoped>
li {
  list-style: none;
}
</style>
