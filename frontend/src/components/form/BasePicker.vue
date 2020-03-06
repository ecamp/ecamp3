<script>

export default {
  name: 'BasePicker',
  inheritAttr: false,
  props: {
    value: { type: String, required: true }
  },
  data () {
    return {
      localValue: this.value,
      showPicker: false
    }
  },
  watch:
  {
    value () {
      this.localValue = this.value
    },
    showPicker () {
      // save value on menu closing
      if (!this.showPicker) {
        if (this.localValue !== this.value) {
          this.$emit('input', this.localValue)
        }
      }
    }
  },
  methods: {
    close () {
      // reset local value
      this.localValue = this.value
      this.showPicker = false
    },
    save () {
      this.showPicker = false
    }
  }
}
</script>
