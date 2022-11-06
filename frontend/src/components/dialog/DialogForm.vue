<template>
  <v-dialog
    content-class="ec-dialog-form"
    :fullscreen="$vuetify.breakpoint.xsOnly"
    eager
    v-bind="$attrs"
    :value="value"
    :max-width="maxWidth"
    v-on="$listeners"
    @input="onInput"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <ValidationObserver v-if="value" ref="validation" v-slot="{ handleSubmit }">
      <!-- ValidationObserver/handleSubmit ensures that doSubmit is only called if there are no validation errors -->
      <v-form @submit.prevent="handleSubmit(doSubmit)">
        <v-card>
          <v-toolbar dense elevation="0" class="ec-dialog-toolbar">
            <v-icon left>
              {{ icon }}
            </v-icon>
            <v-toolbar-title>
              {{ title }}
            </v-toolbar-title>
            <v-btn
              v-if="$vuetify.breakpoint.smAndUp && cancelAction != null"
              icon
              class="ml-auto"
              :title="$tc('global.button.cancel')"
              @click="doCancel"
            >
              <v-icon>mdi-close</v-icon>
              <span class="d-sr-only">{{ $tc('global.button.cancel') }}</span>
            </v-btn>
          </v-toolbar>
          <div class="pa-4">
            <v-skeleton-loader v-if="loading" type="article" />
            <slot v-else />
          </div>

          <v-card-text>
            <!-- error message via slot -->
            <v-alert v-if="$slots.error" text outlined color="warning" icon="mdi-alert">
              <slot name="error" />
            </v-alert>

            <!-- error message via props -->
            <server-error v-else :server-error="error" />
          </v-card-text>

          <v-card-actions>
            <slot name="moreActions" />
            <v-spacer />
            <v-btn
              v-if="cancelVisible && cancelAction != null"
              :color="cancelColor"
              text
              :disabled="!cancelEnabled"
              @click="doCancel"
            >
              {{ cancelLabel }}
            </v-btn>
            <v-btn
              v-if="submitAction != null"
              :color="submitColor"
              type="submit"
              :loading="isSaving"
              :disabled="!submitEnabled"
            >
              <v-icon v-if="!!submitIcon" left>
                {{ submitIcon }}
              </v-icon>
              {{ submitLabel }}
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
import ServerError from '@/components/form/ServerError.vue'

export default {
  name: 'DialogForm',
  components: { ValidationObserver, ServerError },
  props: {
    value: { type: Boolean, required: true },

    icon: { type: String, default: '', required: false },
    title: { type: String, default: '', required: false },

    submitAction: { type: Function, default: null, required: false },
    submitIcon: { type: String, default: 'mdi-check', required: false },
    submitLabel: {
      type: String,
      default: function () {
        return this.$tc('global.button.submit')
      },
      required: false,
    },
    submitColor: { type: String, default: 'primary', required: false },
    submitEnabled: { type: Boolean, default: true, required: false },

    cancelAction: { type: Function, default: null, required: false },
    cancelIcon: { type: String, default: 'mdi-window-close', required: false },
    cancelLabel: {
      type: String,
      default: function () {
        return this.$tc('global.button.cancel')
      },
      required: false,
    },
    cancelColor: { type: String, default: 'secondary', required: false },
    cancelEnabled: { type: Boolean, default: true, required: false },
    cancelVisible: { type: Boolean, default: true, required: false },

    loading: { type: Boolean, default: false, required: false },
    error: { type: [Object, String, Error], default: null, required: false },

    maxWidth: { type: String, default: '600px', required: false },
  },
  data() {
    return {
      isSaving: false,
    }
  },
  watch: {
    value(visible) {
      if (visible) {
        this.$nextTick(() => this.$refs.validation.reset())
      }
    },
  },
  methods: {
    async doSubmit() {
      this.isSaving = true
      await this.submitAction()
      this.isSaving = false
    },
    doCancel() {
      this.isSaving = false
      if (this.cancelAction != null) {
        this.cancelAction()
      }
    },
    onInput(event) {
      // perform cancel action if dialog is dismissed without using the Cancel button
      if (event === false) {
        this.doCancel()
      }
    },
  },
}
</script>

<style lang="scss">
@media #{map-get($display-breakpoints, 'xs-only')} {
  .ec-dialog-form {
    .v-form,
    .v-form > .v-sheet {
      height: 100%;
    }
  }
}
.ec-dialog-toolbar {
  border-bottom: 1px solid #cfd8dc !important;
}
</style>
