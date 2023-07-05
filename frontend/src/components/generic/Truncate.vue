<template>
  <span class="truncate">
    <span><slot /></span>
  </span>
</template>

<script>
export default {
  name: 'Truncate',
}
</script>

<style>
.truncate {
  display: inline-table;
  table-layout: fixed;
  width: 100%;
}

.truncate > * {
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
