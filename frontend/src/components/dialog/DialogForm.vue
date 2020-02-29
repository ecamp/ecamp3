<template>
  <v-dialog
    v-bind="$attrs"
    :value="value"
    v-on="$listeners"
    @input="$emit('input', $event)">
    <v-form @submit.prevent="doSubmit">
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
            v-if="cancelAction != null"
            :color="cancelColor"
            @click="doCancel">
            <v-icon
              v-if="!!cancelIcon"
              left>
              {{ cancelIcon }}
            </v-icon>
            {{ cancelLabel }}
          </v-btn>
          <v-btn
            v-if="submitAction != null"
            :color="submitColor"
            type="submit"
            :loading="isSaving">
            <v-icon
              v-if="!!submitIcon"
              left>
              {{ submitIcon }}
            </v-icon>
            {{ submitLabel }}
          </v-btn>
          <slot name="actions" />
        </v-card-actions>
      </v-card>
    </v-form>
  </v-dialog>
</template>

<script>
export default {
  name: 'DialogForm',
  props: {
    value: { type: Boolean, default: false, required: true },

    icon: { type: String, default: '', required: false },
    title: { type: String, default: '', required: false },

    submitAction: { type: Function, default: null, required: false },
    submitIcon: { type: String, default: 'mdi-check', required: false },
    submitLabel: { type: String, default: 'Submit', required: false },
    submitColor: { type: String, default: 'primary', required: false },

    cancelAction: { type: Function, default: null, required: false },
    cancelIcon: { type: String, default: 'mdi-window-close', required: false },
    cancelLabel: { type: String, default: 'Cancel', required: false },
    cancelColor: { type: String, default: 'secondary', required: false }
  },
  data () {
    return {
      isSaving: false
    }
  },
  methods: {
    async doSubmit () {
      this.isSaving = true
      await this.submitAction()
      this.isSaving = false
    },
    doCancel () {
      this.isSaving = false
      this.cancelAction()
    }
  }
}
</script>
