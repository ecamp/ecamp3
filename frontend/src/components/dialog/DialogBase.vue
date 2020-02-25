<script>
export default {
  name: 'DialogBase',
  props: {
    value: { default: null, required: true }
  },
  data () {
    return {
      entityProperties: [],
      entityData: {},
      entityUri: ''
    }
  },
  computed: {
    visible: {
      get () { return !!this.value },
      set () { this.$emit('input', null) }
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
      this.entityData = {}
      this.entityProperties.forEach(key => {
        this.entityData[key] = data[key]
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
      this.visible = false
    }
  }
}
</script>
