<template>
  <v-dialog
    v-bind="$attrs"
    v-on="$listeners">
    <v-card>
      <v-toolbar dense color="blue-grey lighten-5">
        <v-icon left>
          {{ icon }}
        </v-icon>
        <v-toolbar-title>
          {{ title }}
        </v-toolbar-title>
      </v-toolbar>

      <v-card-text>
        <v-container>
          <slot />
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn
          v-if="create != null"
          color="success"
          @click="doCreate">
          <v-progress-circular
            v-if="isSaving"
            indeterminate
            color="white"
            size="20"
            class="mr-2" />
          Create
        </v-btn>
        <v-btn
          v-if="update != null"
          color="success"
          @click="doUpdate">
          <v-progress-circular
            v-if="isSaving"
            indeterminate
            color="white"
            size="20"
            class="mr-2" />
          Save
        </v-btn>
        <v-btn
          v-if="del != null"
          color="error"
          @click="doDel">
          <v-progress-circular
            v-if="isSaving"
            indeterminate
            color="white"
            size="20"
            class="mr-2" />
          Delete
        </v-btn>
        <v-btn
          v-if="cancel != null"
          color="white"
          @click="doCancel">
          Cancel
        </v-btn>
        <slot name="actions" />
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'DialogForm',
  props: {
    icon: { type: String, default: '', required: false },
    title: { type: String, default: '', required: false },
    create: { type: Function, default: null, required: false },
    update: { type: Function, default: null, required: false },
    del: { type: Function, default: null, required: false },
    cancel: { type: Function, default: null, required: false }
  },
  data () {
    return {
      isSaving: false
    }
  },
  methods: {
    doCreate () {
      this.isSaving = true
      this.create().then(() => {
        this.isSaving = false
      })
    },
    doUpdate () {
      this.isSaving = true
      this.update().then(() => {
        this.isSaving = false
      })
    },
    doDel () {
      this.isSaving = true
      this.del().then(() => {
        this.isSaving = false
      })
    },
    doCancel () {
      this.isSaving = false
      this.cancel()
    }
  }
}
</script>
