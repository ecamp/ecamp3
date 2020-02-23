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
      entityUri: '',
      visible: false
    }
  },
  watch: {
    visible: function (visible) {
      if (!visible) {
        this.$emit('input', null)
      }
    },
    value: function (id) {
      this.visible = (!!id)
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
        this.api.get(uri)._meta.loaded.then(this.setEntityData)
      }
    },
    setEntityData (data) {
      this.entityData = {}
      this.entityProperties.forEach(key => {
        this.entityData[key] = data[key]
      })
    },
    create () {
      return this.api.post(this.entityUri, this.entityData).then(this.cancel)
    },
    update () {
      return this.api.patch(this.entityUri, this.entityData).then(this.cancel)
    },
    del () {
      return this.api.del(this.entityUri).then(this.cancel)
    },
    cancel () {
      this.visible = false
    }
  }
}
</script>
