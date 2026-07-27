<template>
  <v-alert
    class="toast-notification"
    :type="toast.type"
    variant="elevated"
    closable
    role="alert"
    data-testid="toast-notification"
    @click:close="dismiss"
    @mouseenter="pauseTimeout"
    @mouseleave="resumeTimeout"
  >
    <template v-if="typeof toast.content === 'string'">
      {{ toast.content }}
    </template>
    <MultiLineToast v-else v-bind="toast.content" />
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
      remainingTimeout: timeout > 0 ? timeout : null,
      timeoutStartedAt: null,
      timeoutId: null,
    }
  },
  mounted() {
    this.resumeTimeout()
  },
  beforeUnmount() {
    this.clearTimeout()
  },
  methods: {
    clearTimeout() {
      window.clearTimeout(this.timeoutId)
      this.timeoutId = null
    },
    dismiss() {
      this.clearTimeout()
      this.$emit('dismiss', this.toast.id)
    },
    pauseTimeout() {
      if (this.timeoutId === null) {
        return
      }

      this.remainingTimeout -= Date.now() - this.timeoutStartedAt
      this.clearTimeout()
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
    },
  },
}
</script>

<style scoped>
.toast-notification {
  pointer-events: auto;
}
</style>
