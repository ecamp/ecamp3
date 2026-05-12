<template>
  <v-tooltip :disabled="tooltip == ''" location="bottom">
    <template #activator="{ props }">
      <v-btn
        :icon="modelValue ? 'mdi-lock-open-variant' : 'mdi-lock'"
        :aria-label="tooltip"
        :aria-disabled="disabledForGuest"
        size="small"
        :class="{ 'e-shake-lock': shake }"
        v-bind="props"
        @click="onClick"
      />
    </template>
    <span>{{ tooltip }}</span>
  </v-tooltip>
</template>

<script>
export default {
  name: 'LockButton',
  props: {
    modelValue: {
      type: Boolean,
      required: true,
    },
    message: {
      type: String,
      required: false,
      default: null,
    },
    disabledForGuest: {
      type: Boolean,
      required: false,
      default: false,
    },
    shake: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['click'],
  computed: {
    tooltip() {
      if (this.disabledForGuest) {
        return this.$t('components.generic.lockButton.guestsCannotEdit')
      }
      if (this.message) {
        return this.message
      }
      if (!this.modelValue) {
        return this.$t('components.generic.lockButton.clickToUnlock')
      }
      return this.$t('components.generic.lockButton.clickToLock')
    },
  },
  methods: {
    onClick() {
      if (!this.disabledForGuest) {
        this.$emit('click')
      }
    },
  },
}
</script>

<style scoped>
.e-shake-lock {
  animation: horizontal-shaking 0.5s linear 1;
}

@keyframes horizontal-shaking {
  0% {
    transform: translateX(0);
  }
  10% {
    transform: translateX(5px);
  }
  25% {
    transform: translateX(-5px);
  }
  45% {
    transform: translateX(4px);
  }
  65% {
    transform: translateX(-4px);
  }
  80% {
    transform: translateX(3px);
  }
  100% {
    transform: translateX(0);
  }
}
</style>
