<script>
export default {
  name: 'TocConfig',
  props: {
    modelValue: { type: Object, required: true },
  },
  defaultOptions() {
    return {}
  },
  design: {
    multiple: false,
  },
}
</script>
