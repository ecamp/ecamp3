<template>
  <div class="toast-host" aria-live="polite">
    <v-slide-x-reverse-transition group>
      <ToastNotification
        v-for="toast in visibleToasts"
        :key="toast.id"
        class="toast-notification"
        :toast="toast"
        @dismiss="dismissToast"
      />
    </v-slide-x-reverse-transition>
  </div>
</template>

<script>
import ToastNotification from '@/components/toast/ToastNotification.vue'
import { dismissToast, visibleToasts } from '@/components/toast/useToast.js'

export default {
  name: 'ToastHost',
  components: { ToastNotification },
  setup() {
    return { dismissToast, visibleToasts }
  },
}
</script>

<style scoped>
.toast-host {
  position: fixed;
  z-index: 10000;
  top: max(16px, env(safe-area-inset-top));
  right: max(16px, env(safe-area-inset-right));
  display: flex;
  flex-direction: column;
  gap: 12px;
  width: min(400px, calc(100vw - 32px));
  pointer-events: none;
}
.toast-notification {
  pointer-events: auto;
}
</style>
