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
          data[key]()._meta.load.then(obj => this.$set(this.entityData, key, obj._meta.self))
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
      const _events = this._events
      const promise = this.api.post(this.entityUri, this.entityData).then(this.close, e => this.onError(_events, e))
      this.$emit('submit')
      return promise
    },
    update () {
      this.error = null
      const _events = this._events
      const promise = this.api.patch(this.entityUri, this.entityData).then(this.close, e => this.onError(_events, e))
      this.$emit('submit')
      return promise
    },
    del () {
      this.error = null
      const _events = this._events
      const promise = this.api.del(this.entityUri).then(this.close, e => this.onError(_events, e))
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
    onError (originalHandlers, e) {
      // By the time we get here, the dialog might be closed because an enclosing menu might be closed.
      // See https://github.com/vuetifyjs/vuetify/issues/7021
      // In this case, the event handlers in here are cleared, so we need to temporarily restore them
      // to make the $emit work correctly
      const eventHandlers = this._events
      this._events = originalHandlers
      this.$emit('error', e)
      this._events = eventHandlers

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
