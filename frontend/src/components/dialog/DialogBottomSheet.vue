<template>
  <v-bottom-sheet
    content-class="ec-dialog-form"
    eager
    v-bind="$attrs"
    :value="value"
    v-on="$listeners"
    @input="onInput"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <ValidationObserver v-if="value" ref="validation" v-slot="{ handleSubmit }">
      <!-- ValidationObserver/handleSubmit ensures that doSubmit is only called if there are no validation errors -->
      <v-form @submit.prevent="handleSubmit(doSubmit)">
        <v-card rounded="b-0">
          <v-toolbar dense elevation="0" class="ec-dialog-toolbar">
            <v-icon left>
              {{ icon }}
            </v-icon>
            <v-toolbar-title>
              {{ title }}
            </v-toolbar-title>
            <v-btn
              v-if="cancelAction != null"
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

          <v-card-text class="py-0 error-area">
            <!-- error message via slot -->
            <v-alert v-if="$slots.error" text outlined color="warning" icon="mdi-alert">
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
                :loading="currentlySaving"
                :disabled="!submitEnabled"
              >
                <v-icon v-if="!!submitIcon" left>
                  {{ submitIcon }}
                </v-icon>
                {{ submitLabel }}
              </v-btn>
              <slot name="actions" />
            </v-card-actions>
          </div>
        </v-card>
      </v-form>
    </ValidationObserver>
  </v-bottom-sheet>
</template>
<script>
import { ValidationObserver } from 'vee-validate'
import ServerError from '@/components/form/ServerError.vue'
import DialogUiBase from '@/components/dialog/DialogUiBase.vue'

export default {
  name: 'DialogBottomSheet',
  components: { ServerError, ValidationObserver },
  extends: DialogUiBase,
  props: {
    icon: { type: String, default: '', required: false },
    title: { type: String, default: '', required: false },
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
