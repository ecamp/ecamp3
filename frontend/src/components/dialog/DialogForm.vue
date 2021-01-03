<template>
  <v-dialog
    content-class="ec-dialog-form"
    :fullscreen="$vuetify.breakpoint.xsOnly"
    eager
    v-bind="$attrs"
    :value="value"
    v-on="$listeners"
    @input="onInput">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <ValidationObserver v-if="value" ref="validation" v-slot="{ handleSubmit }">
      <!-- ValidationObserver/handleSubmit ensures that doSubmit is only called if there are no validation errors -->
      <v-form @submit.prevent="handleSubmit(doSubmit)">
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
            <slot />
          </v-card-text>

          <v-card-text v-if="$slots.error">
            <v-alert color="red">
              <slot name="error" />
            </v-alert>
          </v-card-text>

          <v-card-actions>
            <slot name="moreActions" />
            <v-spacer />
            <v-btn
              v-if="cancelAction != null"
              :color="cancelColor"
              text
              :disabled="!cancelEnabled"
              @click="doCancel">
              <v-icon
                v-if="!!cancelIcon"
                left>
                {{ cancelIcon }}
              </v-icon>
              {{ $tc(cancelLabel) }}
            </v-btn>
            <v-btn
              v-if="submitAction != null"
              :color="submitColor"
              type="submit"
              :loading="isSaving"
              :disabled="!submitEnabled">
              <v-icon
                v-if="!!submitIcon"
                left>
                {{ submitIcon }}
              </v-icon>
              {{ $tc(submitLabel) }}
            </v-btn>
            <slot name="actions" />
          </v-card-actions>
        </v-card>
      </v-form>
    </ValidationObserver>
  </v-dialog>
</template>

<script>

import { ValidationObserver } from 'vee-validate'

export default {
  name: 'DialogForm',
  components: { ValidationObserver },
  props: {
    value: { type: Boolean, required: true },

    icon: { type: String, default: '', required: false },
    title: { type: String, default: '', required: false },

    submitAction: { type: Function, default: null, required: false },
    submitIcon: { type: String, default: 'mdi-check', required: false },
    submitLabel: { type: String, default: 'global.button.submit', required: false },
    submitColor: { type: String, default: 'primary', required: false },
    submitEnabled: { type: Boolean, default: true, required: false },

    cancelAction: { type: Function, default: null, required: false },
    cancelIcon: { type: String, default: 'mdi-window-close', required: false },
    cancelLabel: { type: String, default: 'global.button.cancel', required: false },
    cancelColor: { type: String, default: 'secondary', required: false },
    cancelEnabled: { type: Boolean, default: true, required: false },

    error: { type: String, default: '', required: false }
  },
  data () {
    return {
      isSaving: false
    }
  },
  watch: {
    value (visible) {
      if (visible) {
        this.$nextTick(() => this.$refs.validation.reset())
      }
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
    },
    onInput (event) {
      // perform cancel action if dialog is dismissed without using the Cancel button
      if (event === false) {
        this.doCancel()
      }
    }
  }
}
</script>

<style lang="scss">
@media #{map-get($display-breakpoints, 'xs-only')}{
  .ec-dialog-form {
    .v-form, .v-form > .v-sheet {
      height: 100%;
    }
  }
}
</style>
