<template>
  <v-dialog
    v-bind="$attrs"
    :fullscreen="$vuetify.display.xs"
    content-class="ec-dialog-form"
    :max-width="maxWidth"
    :model-value
    @update:model-value="onInput"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <Form @submit="doSubmit">
      <v-card>
        <v-toolbar
          class="ec-dialog-toolbar"
          density="compact"
          color="surface"
          elevation="0"
        >
          <template #prepend>
            <v-icon class="ml-3">{{ icon }}</v-icon>
          </template>
          <v-toolbar-title>{{ title }}</v-toolbar-title>
          <v-btn
            v-if="
              (closeVisibleOnMobile || $vuetify.display.smAndUp) && cancelAction != null
            "
            icon
            class="ml-auto"
            :title="$t('global.button.cancel')"
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

        <v-card-text v-if="$slots.error || error">
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
            :text="cancelLabel"
            @click="doCancel"
          />
          <v-btn
            v-if="submitAction != null"
            :color="submitColor"
            variant="elevated"
            :disabled="!submitEnabled"
            :loading="currentlySaving"
            type="submit"
            :prepend-icon="submitIcon"
            :text="submitLabel"
          />
          <slot name="actions" />
        </v-card-actions>
      </v-card>
    </Form>
  </v-dialog>
</template>

<script>
import ServerError from '@/components/form/ServerError.vue'
import DialogUiBase from '@/components/dialog/DialogUiBase.vue'
import { Form } from 'vee-validate'

export default {
  name: 'DialogForm',
  components: {
    Form,
    ServerError,
  },
  extends: DialogUiBase,
  props: {
    icon: { type: String, default: '', required: false },
    title: { type: String, default: '', required: false },

    maxWidth: { type: String, default: '600px', required: false },
  },
  emits: ['update:modelValue', 'update:savingOverride'],
  computed: {
    currentlySaving() {
      return this.isSaving || this.savingOverride
    },
  },
  methods: {
    async doSubmit() {
      this.isSaving = true
      this.$emit('update:savingOverride', true)
      await this.submitAction()
      this.isSaving = false
      this.$emit('update:savingOverride', false)
    },
    doCancel() {
      this.isSaving = false
      this.$emit('update:savingOverride', false)
      if (this.cancelAction != null) {
        this.cancelAction()
      }
    },
    onInput(event) {
      // perform cancel action if dialog is dismissed without using the Cancel button
      if (event === false) {
        this.doCancel()
      }
      this.$emit('update:modelValue', event)
    },
  },
}
</script>

<style lang="scss" scoped>
@use 'vuetify/settings';
@use 'sass:map';

@media #{map.get(settings.$display-breakpoints, 'xs')} {
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
