<template>
  <v-tooltip :disabled="tooltip == ''" bottom>
    <template #activator="{ on }">
      <v-icon v-if="value" small v-on="{ dblclick: iconDblClick, ...on }">
        mdi-lock-open-variant
      </v-icon>
      <v-icon
        v-else
        small
        color="grey"
        :class="{ 'e-shake-lock': shake }"
        v-on="{ dblclick: iconDblClick, ...on }"
      >
        mdi-lock
      </v-icon>
    </template>
    <span>{{ tooltip }}</span>
  </v-tooltip>
</template>

<script>
export default {
  name: 'LockIcon',
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
        return this.$tc('components.generic.lockIcon.guestsCannotEdit')
      }
      if (this.message) {
        return this.message
      }
      if (!this.value) {
        return this.$tc('components.generic.lockIcon.doubleClickToUnlock')
      }
      return ''
    },
  },
  methods: {
    iconDblClick() {
      if (!this.disabledForGuest) {
        this.$emit('dblclick')
      }
    },
  },
}
</script>

<style scoped>
.v-icon {
  cursor: pointer;
}

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
