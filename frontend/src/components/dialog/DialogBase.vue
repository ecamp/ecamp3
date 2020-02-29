<script>
export default {
  name: 'DialogBase',
  props: {
    value: { type: Boolean, default: false, required: true }
  },
  data () {
    return {
      entityProperties: [],
      entityData: {},
      entityUri: ''
    }
  },
  methods: {
    clearEntityData () {
      this.setEntityData({})
    },
    loadEntityData (uri) {
      this.clearEntityData()
      if (uri) {
        this.entityUri = uri
        this.api.get(uri)._meta.load.then(this.setEntityData)
      }
    },
    setEntityData (data) {
      this.entityProperties.forEach(key => {
        this.$set(this.entityData, key, data[key])
      })
    },
    create () {
      return this.api.post(this.entityUri, this.entityData).then(this.close)
    },
    update () {
      return this.api.patch(this.entityUri, this.entityData).then(this.close)
    },
    del () {
      return this.api.del(this.entityUri).then(this.close)
    },
    close () {
      this.$emit('input', false)
    }
  }
}
</script>
