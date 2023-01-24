<template>
  <div />
</template>

<script>
export default {
  name: 'TocConfig',
  props: {
    value: { type: Object, required: true },
  },
  defaultOptions() {
    return {}
  },
  design: {
    multiple: false,
  },
}
</script>
