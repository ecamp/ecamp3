<script>
import { mapValues } from 'lodash-es'

export default {
  name: 'DialogBase',
  props: {
    // manual error handler to generate error message
    // should return an error message as string (or null, in which case the default error message is displayed)
    errorHandler: { type: Function, required: false, default: null },

    successHandler: { type: Function, required: false, default: null },
  },
  emits: ['opened', 'closed', 'submit', 'success', 'error'],
  data() {
    return {
      // specifies entity properties available in the form
      entityProperties: [],

      // single selection of related entities (e.g. activity.category)
      // suitable for ManyToOne relations. Property is sent as IRI to the API.
      embeddedEntities: [],

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
    setOpen(open) {
      this.showDialog = open
    },
    async loadEntityData(uri) {
      this.clearEntityData()
      if (uri) {
        this.entityUri = uri
        const entity = await this.api.get(uri)._meta.load
        this.setEntityData(entity.$toData())
      }
    },
    setEntityData(data) {
      this.entityData = mapValues(data, (value, key) =>
        this.embeddedEntities.includes(key) && value?._meta ? value._meta.self : value
      )
      this.loading = false
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
      const updatePayload = {}
      for (const property of [...this.entityProperties, ...this.embeddedEntities]) {
        updatePayload[property] = payloadData[property]
      }

      const promise = this.api
        .patch(this.entityUri, updatePayload)
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
    onSuccess(response) {
      this.$emit('success')
      if (this.successHandler) {
        this.successHandler(response)
      }
      this.close()
      return response
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
          defaultMessage = this.$t('global.serverError.409')
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
