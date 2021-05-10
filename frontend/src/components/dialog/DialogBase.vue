<script>
export default {
  name: 'DialogBase',
  data () {
    return {
      entityProperties: [],
      embeddedEntities: [],
      embeddedCollections: [],
      entityData: {},
      entityUri: '',
      showDialog: false,
      loading: true,
      error: null
    }
  },
  watch: {
    showDialog (visible) {
      if (visible) {
        this.error = null
      }
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
        if (data[key]) {
          data[key]()._meta.load.then(obj => this.$set(this.entityData, key + 'Id', obj.id))
        }
      })
      this.embeddedCollections.forEach(key => {
        if (data[key]) {
          data[key]()._meta.load.then(obj => this.$set(this.entityData, key, obj.items))
        }
      })
      this.loading = false
    },
    create () {
      this.error = null
      const promise = this.api.post(this.entityUri, this.entityData).then(this.close, this.onError)
      this.$emit('submit')
      return promise
    },
    update () {
      this.error = null
      const promise = this.api.patch(this.entityUri, this.entityData).then(this.close, this.onError)
      this.$emit('submit')
      return promise
    },
    del () {
      this.error = null
      const promise = this.api.del(this.entityUri).then(this.close, this.onError)
      this.$emit('submit')
      return promise
    },
    onSuccess () {
      this.$emit('success')
      this.close()
    },
    close () {
      this.showDialog = false
    },
    onError (e) {
      this.$emit('error', e)
      this.error = e.message
      if (e.response) {
        if (e.response.status === 409 /* Conflict */) {
          this.error = this.$tc('global.serverError.409')
        }
        if (e.response.status === 422 /* Validation Error */) {
          this.error = e
        }
      }
    }
  }
}
</script>
