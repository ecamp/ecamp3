<template>
  <v-avatar :size="size" :color="color">
    <span class="d-sr-only">{{ user.displayName }}</span>
    <span aria-hidden="true" :style="style">{{ initials }}</span>
  </v-avatar>
</template>
<script>
import campCollaborationInitials from '@/common/helpers/campCollaborationInitials.js'
import {
  contrastColor,
  convertHslColor,
  hslToStringColor,
  userHslColor,
  campCollaborationHslColor,
} from '@/common/helpers/colors.js'
import userInitials from '@/common/helpers/userInitials.js'

export default {
  name: 'UserAvatar',
  props: {
    size: { type: [Number, String], required: false, default: 48 },
    user: { type: Object, default: null },
    campCollaboration: { type: Object, default: null },
  },
  computed: {
    isLoading() {
      return (this.user || this.campCollaboration)?._meta?.loading
    },
    hslColor() {
      if (this.isLoading) {
        return [0, 0, 0.1]
      }
      return this.user
        ? userHslColor(this.user)
        : campCollaborationHslColor(this.campCollaboration)
    },
    color() {
      return hslToStringColor(...this.hslColor)
    },
    initials() {
      if (this.isLoading) {
        return ''
      }
      return this.user
        ? userInitials(this.user)
        : campCollaborationInitials(this.campCollaboration)
    },
    style() {
      return {
        color: contrastColor(...convertHslColor(...this.hslColor)),
        fontSize: Number(this.size) / 2.4 + 'px',
        fontWeight: Number(this.size) >= 40 ? 400 : 600,
        letterSpacing: '.1em',
        marginRight: '-.125em',
      }
    },
  },
}
</script>
