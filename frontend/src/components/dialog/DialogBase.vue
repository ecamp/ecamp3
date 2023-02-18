<script>
export default {
  name: 'DialogBase',
  props: {
    // manual error handler to generate error message
    // should return an error message as string (or null, in which case the default error message is displayed)
    errorHandler: { type: Function, required: false, default: null },
  },
  data() {
    return {
      // specifies entity properties available in the form
      entityProperties: [],

      // single selection of related entities (e.g. activity.category)
      // suitable for ManyToOne relations. Property is sent as IRI to the API.
      embeddedEntities: [],

      // multi selection of related entities (e.g. category.preferredContentTypes)
      // suitable for ManyToMany relations. Property is sent as array of IRIs to the API
      // not suitable, if data of the embedded entities should be edited as well (e.g. OneToMany relations)
      embeddedCollections: [],

      entityData: {},
      entityUri: '',
      showDialog: false,
      loading: true,
      error: null,
    }
  },
  watch: {
    showDialog(visible) {
      if (visible) {
        this.error = null
        this.$emit('opened')
      } else {
        this.$emit('closed')
      }
    },
  },
  methods: {
    clearEntityData() {
      this.loading = true
      this.entityData = {}
    },
    loadEntityData(uri) {
      this.clearEntityData()
      if (uri) {
        this.entityUri = uri
        this.api.get(uri)._meta.load.then(this.setEntityData)
      }
    },
    setEntityData(data) {
      const loadingPromises = []

      this.entityProperties.forEach((key) => {
        this.$set(this.entityData, key, data[key])
      })
      this.embeddedEntities.forEach((key) => {
        if (data[key]) {
          loadingPromises.push(data[key]()._meta.load)
          data[key]()._meta.load.then((obj) =>
            this.$set(this.entityData, key, obj._meta.self)
          )
        }
      })
      this.embeddedCollections.forEach((key) => {
        if (data[key]) {
          loadingPromises.push(data[key]().$loadItems())
          data[key]()
            .$loadItems()
            .then((obj) => {
              this.$set(
                this.entityData,
                key,
                obj.items.map((entity) => entity._meta.self)
              )
            })
        }
      })

      // wait for all loading promises to finish before showing any content
      Promise.all(loadingPromises).then(() => {
        this.loading = false
      })
    },
    create(payloadData = null) {
      this.error = null
      const _events = this._events
      payloadData ??= this.entityData
      const promise = this.api
        .post(this.entityUri, payloadData)
        .then(this.onSuccess, (e) => this.onError(_events, e))
      this.$emit('submit')
      return promise
    },
    update(payloadData = null) {
      this.error = null
      const _events = this._events
      payloadData ??= this.entityData
      const promise = this.api
        .patch(this.entityUri, payloadData)
        .then(this.onSuccess, (e) => this.onError(_events, e))
      this.$emit('submit')
      return promise
    },
    del() {
      this.error = null
      const _events = this._events
      const promise = this.api
        .del(this.entityUri)
        .then(this.onSuccess, (e) => this.onError(_events, e))
      this.$emit('submit')
      return promise
    },
    onSuccess() {
      this.$emit('success')
      this.close()
    },
    close() {
      this.showDialog = false
    },
    open() {
      this.showDialog = true
    },
    onError(originalHandlers, e) {
      // By the time we get here, the dialog might be closed because an enclosing menu might be closed.
      // See https://github.com/vuetifyjs/vuetify/issues/7021
      // In this case, the event handlers in here are cleared, so we need to temporarily restore them
      // to make the $emit work correctly
      const eventHandlers = this._events
      this._events = originalHandlers
      this.$emit('error', e)
      this._events = eventHandlers

      // default error message
      let defaultMessage = e.message
      if (e.response) {
        if (e.response.status === 409 /* Conflict */) {
          defaultMessage = this.$tc('global.serverError.409')
        }
        if (e.response.status === 422 /* Validation Error */) {
          defaultMessage = e
        }
      }

      // overwrite with manual error message from errorHandler
      let errorHandlerMessage = null
      if (this.errorHandler) {
        errorHandlerMessage = this.errorHandler(e)
      }

      this.error = errorHandlerMessage || defaultMessage
    },
  },
}
</script>
