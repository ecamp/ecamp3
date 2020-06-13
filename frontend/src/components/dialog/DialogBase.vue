<script>
export default {
  name: 'DialogBase',
  data () {
    return {
      entityProperties: [],
      embeddedEntities: [],
      entityData: {},
      entityUri: '',
      showDialog: false,
      loading: true
    }
  },
  methods: {
    clearEntityData () {
      this.loading = true
      this.entityData = {}
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
      this.embeddedEntities.forEach(key => {
        data[key]()._meta.load.then(obj => this.$set(this.entityData, key + 'Id', obj.id))
      })
      this.loading = false
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
      this.showDialog = false
    }
  }
}
</script>
