<template>
  <v-avatar :size="size" :color="color">
    <span class="white--text font-weight-medium" :style="style">{{ initials }}</span>
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
    size: { type: Number, required: false, default: 48 },
    user: { type: Object, default: null },
    campCollaboration: { type: Object, default: null },
  },
  computed: {
    isLoading() {
      return (this.user || this.campCollaboration)?._meta.loading
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
        fontSize: this.size / 2.5 + 'px',
        fontWeight: 400,
        letterSpacing: '1px',
        marginRight: '-1.5px',
      }
    },
  },
}
</script>
