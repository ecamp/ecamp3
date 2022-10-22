<template>
  <v-tooltip :disabled="hideTooltip" bottom>
    <template #activator="{ on }">
      <v-icon v-if="value" small v-on="on"> mdi-lock-open-variant </v-icon>
      <v-icon v-else small color="grey" v-on="on"> mdi-lock </v-icon>
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
  },
}
</script>
