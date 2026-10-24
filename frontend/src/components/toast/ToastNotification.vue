<template>
  <v-alert
    class="relative bg-surface"
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
    <template v-if="typeof toast.content === 'string'">
      {{ toast.content }}
    </template>
    <MultiLineToast v-else v-bind="toast.content" />
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

<style scoped></style>
