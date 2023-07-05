<template>
  <v-menu
    v-model="open"
    :content-class="contentClass"
    offset-y
    :close-on-content-click="false"
    :close-on-click="true"
    nudge-left="1"
    min-width="300"
    v-bind="{ ...$attrs, ...positions }"
    @input="onInput"
  >
    <template #activator="{ attrs, on }">
      <slot name="activator" v-bind="{ attrs, on }" />
    </template>
    <div class="ec-activator" @click="open = false">
      <slot name="activator" />
    </div>
    <v-alert
      :border="y === 'top' ? 'bottom' : 'bottom'"
      colored-border
      type="error"
      class="mb-0"
      :class="{
        'pb-5 rounded-tr-0': y === 'bottom' && x === 'left',
        'pb-5 rounded-tl-0': y === 'bottom' && x === 'right',
        'pb-5 rounded-br-0': y === 'top' && x === 'left',
        'pb-5 rounded-bl-0': y === 'top' && x === 'right',
      }"
    >
      <slot />
      <v-alert v-if="$slots.error" text outlined color="warning" icon="mdi-alert">
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
    x: {
      type: String,
      default: 'right',
    },
    y: {
      type: String,
      default: 'top',
    },
  },
  data: () => ({
    open: false,
  }),
  computed: {
    positions() {
      const positions = {}
      if (this.x === 'left') {
        positions.left = true
      } else if (this.x === 'right') {
        positions.right = true
      }
      if (this.y === 'top') {
        positions.top = true
        positions.nudgeBottom = 10
      } else if (this.y === 'bottom') {
        positions.bottom = true
      }
      return positions
    },
    contentClass() {
      return `ec-popover-prompt ${
        this.x === 'left'
          ? this.y === 'bottom'
            ? 'ec-popover-prompt--topright'
            : 'ec-popover-prompt--bottomright'
          : this.y === 'bottom'
          ? 'ec-popover-prompt--topleft'
          : 'ec-popover-prompt--bottomleft'
      }`
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

.ec-popover-prompt ::v-deep .ec-activator .v-btn {
  position: absolute;
  background-color: white !important;
  color: #424242 !important;
}

.ec-popover-prompt ::v-deep .ec-activator .v-btn:hover::before {
  opacity: 0;
}

.ec-popover-prompt--topleft ::v-deep .ec-activator .v-btn {
  left: 0;
}

.ec-popover-prompt--topright ::v-deep .ec-activator .v-btn {
  right: 0;
}

.ec-popover-prompt--topleft ::v-deep .ec-activator .v-btn,
.ec-popover-prompt--topright ::v-deep .ec-activator .v-btn {
  bottom: 100%;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}

.ec-popover-prompt--bottomleft ::v-deep .ec-activator .v-btn,
.ec-popover-prompt--bottomright ::v-deep .ec-activator .v-btn {
  top: calc(100% - 10px);
  z-index: 10;
  left: 0;
  border-top-right-radius: 0;
  border-top-left-radius: 0;
}
</style>
