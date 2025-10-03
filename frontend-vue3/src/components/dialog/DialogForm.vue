<template>
  <v-dialog
    :fullscreen="$vuetify.display.xsOnly"
    content-class="ec-dialog-form"
    :model-value="value"
    eager
    v-bind="$attrs"
    :max-width="maxWidth"
    v-on="$listeners"
    @update:model-value="onInput"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <!--    <ValidationObserver v-if="value" ref="validation" v-slot="{ handleSubmit }">-->
    <!-- ValidationObserver/handleSubmit ensures that doSubmit is only called if there are no validation errors -->
    <v-form @submit.prevent="handleSubmit(doSubmit)">
      <v-card>
        <v-toolbar class="ec-dialog-toolbar" dense elevation="0">
          <v-icon start>
            {{ icon }}
          </v-icon>
          <v-toolbar-title>
            {{ title }}
          </v-toolbar-title>
          <v-btn
            v-if="$vuetify.display.smAndUp && cancelAction != null"
            :title="$t('global.button.cancel')"
            class="ml-auto"
            icon
            @click="doCancel"
          >
            <v-icon>mdi-close</v-icon>
            <span class="d-sr-only">{{ $t('global.button.cancel') }}</span>
          </v-btn>
        </v-toolbar>
        <div class="pa-4">
          <v-skeleton-loader v-if="loading" type="article" />
          <slot v-else />
        </div>

        <v-card-text>
          <!-- error message via slot -->
          <v-alert
            v-if="$slots.error"
            color="warning"
            icon="mdi-alert"
            text
            variant="outlined"
          >
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
            :disabled="!cancelEnabled"
            variant="text"
            @click="doCancel"
          >
            {{ cancelLabel }}
          </v-btn>
          <v-btn
            v-if="submitAction != null"
            :color="submitColor"
            :disabled="!submitEnabled"
            :loading="currentlySaving"
            type="submit"
          >
            <v-icon v-if="!!submitIcon" start>
              {{ submitIcon }}
            </v-icon>
            {{ submitLabel }}
          </v-btn>
          <slot name="actions" />
        </v-card-actions>
      </v-card>
    </v-form>
    <!--    </ValidationObserver>-->
  </v-dialog>
</template>

<script>
// import { ValidationObserver } from 'vee-validate'
import ServerError from '@/components/form/ServerError.vue'
import DialogUiBase from '@/components/dialog/DialogUiBase.vue'

export default {
  name: 'DialogForm',
  components: {
    // ValidationObserver,
    ServerError,
  },
  extends: DialogUiBase,
  props: {
    icon: { type: String, default: '', required: false },
    title: { type: String, default: '', required: false },

    maxWidth: { type: String, default: '600px', required: false },
  },
  computed: {
    currentlySaving() {
      return this.isSaving || this.savingOverride
    },
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
      this.$emit('update:saving-override', true)
      await this.submitAction()
      this.isSaving = false
      this.$emit('update:saving-override', false)
    },
    doCancel() {
      this.isSaving = false
      this.$emit('update:saving-override', false)
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

<style lang="scss" scoped>
@use '/src/scss/variables';

@media #{map-get(variables.$display-breakpoints, 'xs')} {
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
