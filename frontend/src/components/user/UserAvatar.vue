<template>
  <v-avatar :size="size" :color="color">
    <span class="d-sr-only">{{ displayName }}</span>
    <span aria-hidden="true" :style="style">{{ initials }}</span>
  </v-avatar>
</template>
<script>
import campCollaborationInitials from '@/common/helpers/campCollaborationInitials.js'
import {
  contrastColor,
  userColor,
  campCollaborationColor,
  defaultColor,
} from '@/common/helpers/colors.js'
import userInitials from '@/common/helpers/userInitials.js'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'
import userDisplayName from '@/common/helpers/userDisplayName.js'

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
    color() {
      if (this.isLoading) {
        return defaultColor
      }
      return this.user
        ? userColor(this.user)
        : campCollaborationColor(this.campCollaboration)
    },
    initials() {
      if (this.isLoading) {
        return ''
      }
      return this.user
        ? userInitials(this.user)
        : campCollaborationInitials(this.campCollaboration)
    },
    displayName() {
      if (this.isLoading) {
        return ''
      }
      return this.user
        ? userDisplayName(this.user)
        : campCollaborationDisplayName(this.campCollaboration)
    },
    style() {
      return {
        color: contrastColor(this.color),
        fontSize: Number(this.size) / 2.4 + 'px',
        fontWeight: Number(this.size) > 44 ? 450 : Number(this.size) > 24 ? 500 : 600,
        letterSpacing: Number(this.size) < 24 ? '.05em' : 0,
        marginRight: Number(this.size) < 24 ? '-.125em' : 0,
      }
    },
  },
}
</script>
