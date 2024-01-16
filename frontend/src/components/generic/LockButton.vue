<template>
  <v-tooltip :disabled="tooltip == ''" location="bottom">
    <template #activator="{ on }">
      <button
        :aria-label="tooltip"
        :aria-disabled="disabledForGuest"
        class="v-btn v-btn--icon v-btn--round v-size--small"
        :class="{ 'e-shake-lock': shake }"
        @click="onClick"
        v-on="on"
      >
        <v-icon v-if="value" size="small">mdi-lock-open-variant</v-icon>
        <v-icon v-else size="small">mdi-lock</v-icon>
      </button>
    </template>
    <span>{{ tooltip }}</span>
  </v-tooltip>
</template>

<script>
export default {
  name: 'LockButton',
  props: {
    value: {
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
  computed: {
    tooltip() {
      if (this.disabledForGuest) {
        return this.$tc('components.generic.lockButton.guestsCannotEdit')
      }
      if (this.message) {
        return this.message
      }
      if (!this.value) {
        return this.$tc('components.generic.lockButton.clickToUnlock')
      }
      return this.$tc('components.generic.lockButton.clickToLock')
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
