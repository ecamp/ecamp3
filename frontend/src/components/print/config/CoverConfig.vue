<script>
export default {
  name: 'CoverConfig',
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
