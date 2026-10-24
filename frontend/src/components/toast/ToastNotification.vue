<template>
  <v-alert
    class="relative bg-surface"
    :class="{ 'toast-with-actions': toast.actions }"
    :type="toast.type"
    :variant="toast.type === 'error' ? 'elevated' : 'tonal'"
    closable
    :role="toast.type === 'error' ? 'alert' : 'status'"
    data-testid="toast-notification"
    @click="cancelTimeout"
    @click:close="dismiss"
    @mouseenter="pauseTimeout"
    @mouseleave="resumeTimeout"
  >
    <div class="toast-content">
      <template v-if="typeof toast.content === 'string'">
        {{ toast.content }}
      </template>
      <MultiLineToast v-else v-bind="toast.content" />
    </div>
    <div
      v-if="toast.actions"
      class="toast-actions d-flex flex-wrap justify-end ga-1 mt-1"
    >
      <v-btn
        v-for="action in toast.actions"
        :key="action.label"
        variant="text"
        density="comfortable"
        slim
        data-testid="toast-action"
        @click="runAction(action)"
      >
        {{ action.label }}
      </v-btn>
    </div>
    <template #close="{ props }">
      <v-btn
        v-bind="props"
        :variant="remainingTimeout !== null ? 'text' : 'tonal'"
        @click.stop
      >
        <v-progress-circular
          v-if="remainingTimeout !== null"
          class="absolute pointer-events-none opacity-50"
          :model-value="100 - timeoutProgress"
          :size="32"
          :width="2"
          aria-hidden="true"
        />
        <v-icon icon="$close" :size="remainingTimeout !== null ? 'small' : 'default'" />
      </v-btn>
    </template>
  </v-alert>
</template>

<script>
import MultiLineToast from '@/components/toast/MultiLineToast.vue'

export default {
  name: 'ToastNotification',
  components: { MultiLineToast },
  props: {
    toast: {
      type: Object,
      required: true,
    },
  },
  emits: ['dismiss'],
  data() {
    const timeout = Number(this.toast.timeout)

    return {
      initialTimeout: timeout > 0 ? timeout : null,
      remainingTimeout: timeout > 0 ? timeout : null,
      timeoutProgress: timeout > 0 ? 100 : 0,
      progressIntervalId: null,
      timeoutStartedAt: null,
      timeoutId: null,
    }
  },
  mounted() {
    this.resumeTimeout()
  },
  beforeUnmount() {
    this.clearTimers()
  },
  methods: {
    clearTimers() {
      window.clearInterval(this.progressIntervalId)
      window.clearTimeout(this.timeoutId)
      this.progressIntervalId = null
      this.timeoutId = null
    },
    runAction(action) {
      action.onClick()
      this.dismiss()
    },
    cancelTimeout() {
      this.clearTimers()
      this.remainingTimeout = null
    },
    dismiss() {
      this.timeoutProgress = 0
      this.clearTimers()
      this.$emit('dismiss', this.toast.id)
    },
    getCurrentRemainingTimeout() {
      return Math.max(this.remainingTimeout - (Date.now() - this.timeoutStartedAt), 0)
    },
    pauseTimeout() {
      if (this.timeoutId === null) {
        return
      }

      this.remainingTimeout = this.getCurrentRemainingTimeout()
      this.clearTimers()
      this.updateTimeoutProgress()
    },
    resumeTimeout() {
      if (
        this.timeoutId !== null ||
        this.remainingTimeout === null ||
        !Number.isFinite(this.remainingTimeout)
      ) {
        return
      }

      this.timeoutStartedAt = Date.now()
      this.timeoutId = window.setTimeout(() => this.dismiss(), this.remainingTimeout)
      this.progressIntervalId = window.setInterval(
        () => this.updateTimeoutProgress(),
        100
      )
    },
    updateTimeoutProgress() {
      const remainingTimeout =
        this.timeoutId === null
          ? this.remainingTimeout
          : this.getCurrentRemainingTimeout()
      this.timeoutProgress = (remainingTimeout / this.initialTimeout) * 100
    },
  },
}
</script>

<style scoped>
.toast-with-actions {
  grid-template-areas:
    'prepend content append close'
    '. actions actions actions';
  grid-template-columns: max-content minmax(0, 1fr) max-content max-content;
}

.toast-with-actions :deep(.v-alert__content) {
  display: contents;
}

.toast-with-actions .toast-content {
  grid-area: content;
  align-self: center;
}

.toast-actions {
  grid-area: actions;
}
</style>
