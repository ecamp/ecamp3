<template>
  <v-bottom-sheet
    v-bind="$attrs"
    content-class="ec-dialog-form"
    eager
    :model-value
    @update:model-value="onInput"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <Form @submit="doSubmit">
      <v-card rounded="b-0">
        <v-toolbar class="ec-dialog-toolbar" density="compact" elevation="0">
          <v-icon start>
            {{ icon }}
          </v-icon>
          <v-toolbar-title>
            {{ title }}
          </v-toolbar-title>
          <v-btn
            v-if="cancelAction != null"
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

        <v-card-text class="py-0 error-area">
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

        <slot name="afterErrors" />

        <div class="d-flex flex-wrap justify-end">
          <v-card-actions>
            <slot name="moreActions" />
          </v-card-actions>
          <v-spacer />
          <v-card-actions>
            <v-spacer />
            <v-btn
              v-if="cancelVisible && cancelAction != null"
              :color="cancelColor"
              :disabled="!cancelEnabled"
              variant="text"
              @click="doCancel"
            >
              {{ cancelLabelOrDefault }}
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
              {{ submitLabelOrDefault }}
            </v-btn>
            <slot name="actions" />
          </v-card-actions>
        </div>
      </v-card>
    </Form>
  </v-bottom-sheet>
</template>
<script>
import ServerError from '@/components/form/ServerError.vue'
import DialogUiBase from '@/components/dialog/DialogUiBase.vue'
import { Form } from 'vee-validate'

export default {
  name: 'DialogBottomSheet',
  components: {
    Form,
    ServerError,
  },
  extends: DialogUiBase,
  props: {
    icon: { type: String, default: '', required: false },
    title: { type: String, default: '', required: false },
  },
  emits: ['update:savingOverride', 'update:modelValue'],
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
<style scoped>
.error-area:empty {
  display: none;
}
:deep(.ec-dialog-form.v-bottom-sheet.v-dialog) {
  overflow-y: auto;
}
.ec-dialog-toolbar {
  position: sticky;
  z-index: 10;
  top: 0;
}
</style>
