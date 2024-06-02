<template>
  <v-menu
    v-model="open"
    :content-class="contentClass"
    offset-y
    :close-on-content-click="false"
    :close-on-click="true"
    allow-overflow
    v-bind="{ ...$attrs, ...positions }"
    @input="onInput"
  >
    <template #activator="{ attrs, on }">
      <slot name="activator" v-bind="{ attrs, on }" />
    </template>
    <div class="ec-activator v-card__actions pa-0" @click="open = false">
      <slot name="activator" />
    </div>
    <v-alert
      border="bottom"
      colored-border
      :type="type"
      class="mb-0 pb-5"
      :class="{
        'rounded-tr-0': position === 'bottom' && align === 'right',
        'rounded-tl-0': position === 'bottom' && align === 'left',
        'rounded-br-0': position === 'top' && align === 'right',
        'rounded-bl-0': position === 'top' && align === 'left',
      }"
    >
      <slot />
      <v-alert v-if="$slots.error" text outlined :color="color" icon="mdi-alert">
        <slot name="error" />
      </v-alert>
      <div class="ec-prompt-buttons mt-2">
        <v-btn
          v-if="cancelVisible && cancelAction != null"
          :color="cancelColor"
          text
          :disabled="!cancelEnabled"
          class="v-btn--has-bg"
          @click="doCancel"
        >
          {{ cancelLabel }}
        </v-btn>
        <v-btn
          v-if="submitAction !== null"
          :color="submitColor"
          type="submit"
          :loading="isSaving"
          :disabled="!submitEnabled"
          @click="doSubmit"
        >
          <v-icon v-if="!!submitIcon" left>
            {{ submitIcon }}
          </v-icon>
          {{ submitLabel }}
        </v-btn>
        <slot name="actions" />
      </div>
    </v-alert>
  </v-menu>
</template>

<script>
import DialogUiBase from '@/components/dialog/DialogUiBase.vue'

export default {
  name: 'PopoverPrompt',
  extends: DialogUiBase,
  props: {
    position: {
      type: String,
      default: 'bottom',
    },
    align: {
      type: String,
      default: 'right',
    },
    type: {
      type: String,
      default: 'info',
    },
  },
  data: () => ({
    open: false,
  }),
  computed: {
    color() {
      if (this.type === 'error') {
        return 'error'
      }

      return 'primary'
    },
    positions() {
      const positions = {}
      if (this.align === 'left') {
        positions.right = true
      } else if (this.align === 'right') {
        positions.left = true
      }
      if (this.position === 'top') {
        positions.nudgeBottom = 10
        positions.top = true
      } else if (this.position === 'bottom') {
        positions.bottom = true
      }
      return positions
    },
    contentClass() {
      return `ec-popover-prompt ec-popover-prompt--position-${this.position} ec-popover-prompt--align-${this.align}`
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
      this.open = false
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
.ec-popover-prompt {
  overflow: visible;
  contain: initial;
  max-width: 90%;
}

.ec-prompt-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.ec-prompt-buttons .v-btn {
  flex-grow: 1;
}

.ec-popover-prompt ::v-deep(.ec-activator .v-btn) {
  position: absolute;
  background-color: white !important;
  color: #424242 !important;
}

.ec-popover-prompt ::v-deep(.ec-activator .v-btn:hover::before) {
  opacity: 0;
}

.ec-popover-prompt--align-left ::v-deep(.ec-activator .v-btn) {
  left: 0;
}

.ec-popover-prompt--align-right ::v-deep(.ec-activator .v-btn) {
  right: 0;
}

.ec-popover-prompt--position-bottom ::v-deep(.ec-activator .v-btn) {
  bottom: 100%;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
  box-shadow:
    0 5px 5px -3px rgba(0, 0, 0, 0.2),
    0 8px 10px 1px rgba(0, 0, 0, 0.14),
    0 3px 14px 2px rgba(0, 0, 0, 0.12);
}

.ec-popover-prompt--position-top ::v-deep(.ec-activator .v-btn) {
  top: calc(100% - 10px);
  z-index: 10;
  border-top-right-radius: 0;
  border-top-left-radius: 0;
  box-shadow:
    0 5px 5px -3px rgba(0, 0, 0, 0.2),
    0 10px 10px 1px rgba(0, 0, 0, 0.14),
    0 14px 14px 0 rgba(0, 0, 0, 0.12);
}
</style>
