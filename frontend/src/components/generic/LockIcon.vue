<template>
  <v-tooltip :disabled="hideTooltip" bottom>
    <template #activator="{ on }">
      <v-icon v-if="value" small v-on="{ dblclick: iconDblClick, ...on }">
        mdi-lock-open-variant
      </v-icon>
      <v-icon
        v-else
        small
        color="grey"
        :title="$tc('components.generic.lockIcon.doubleClickToUnlock')"
        :class="{ 'e-shake-lock': shake }"
        v-on="{ dblclick: iconDblClick, ...on }"
      >
        mdi-lock
      </v-icon>
    </template>
    <span>{{ message || $tc('components.generic.lockIcon.guestsCannotEdit') }}</span>
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
    hideTooltip: {
      type: Boolean,
      required: false,
      default: false,
    },
    shake: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    iconDblClick() {
      this.$emit('dblclick')
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
