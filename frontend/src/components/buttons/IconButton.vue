<template>
  <v-btn
    class="px-3 px-sm-4"
    :class="{ 'v-btn--has-bg': bg }"
    min-width="0"
    :color="color"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <GenericIcon v-if="!iconLast" :left="!hideLabel" size="150%" :spin="animate">
      {{ icon }}
    </GenericIcon>
    <span :class="{ 'd-sr-only': hideLabel }">
      <slot />
    </span>
    <GenericIcon v-if="iconLast" :right="!hideLabel" size="150%" :spin="animate">
      {{ icon }}
    </GenericIcon>
  </v-btn>
</template>

<script>
import GenericIcon from '@/components/generic/GenericIcon.vue'

export default {
  name: 'IconButton',
  components: { GenericIcon },
  props: {
    icon: { type: String, required: true },
    iconLast: { type: Boolean, default: false },
    hideLabel: { type: Boolean, default: false },
    color: { type: String, default: 'normal' },
    bg: { type: Boolean, default: false },
    animate: { type: Boolean, default: false },
  },
}
</script>
