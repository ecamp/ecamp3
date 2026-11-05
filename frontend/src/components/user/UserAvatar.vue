<template>
  <v-avatar :color="color" :size="size" :title="displayName" v-bind="$attrs">
    <span :aria-hidden="shortAria ? null : 'true'" :style="style">{{ initials }}</span>
    <span v-if="shortAria" class="d-sr-only">{{ displayName }}</span>
  </v-avatar>
</template>
<script>
import campCollaborationInitials from '@/helpers/campCollaborationInitials.js'
import {
  contrastColor,
  userColor,
  campCollaborationColor,
  defaultColor,
} from '@/helpers/colors.js'
import { range } from '@/helpers/interpolation.js'
import userInitials from '@/helpers/userInitials.js'
import campCollaborationDisplayName from '@/helpers/campCollaborationDisplayName.js'
import userDisplayName from '@/helpers/userDisplayName.js'

export default {
  name: 'UserAvatar',
  props: {
    size: { type: [Number, String], required: false, default: 48 },
    user: { type: Object, default: null },
    campCollaboration: { type: Object, default: null },
    shortAria: { type: Boolean, default: false },
  },
  computed: {
    isLoading() {
      if (this.user || this.campCollaboration) {
        return (this.user || this.campCollaboration)?._meta?.loading
      }
      return true
    },
    color() {
      if (this.isLoading) {
        return defaultColor()
      }
      return this.campCollaboration
        ? campCollaborationColor(this.campCollaboration)
        : userColor(this.user)
    },
    initials() {
      if (this.isLoading) {
        return ''
      }
      return this.campCollaboration
        ? campCollaborationInitials(this.campCollaboration)
        : userInitials(this.user)
    },
    displayName() {
      if (this.isLoading) {
        return ''
      }
      return this.campCollaboration
        ? campCollaborationDisplayName(this.campCollaboration, this.$t.bind(this))
        : userDisplayName(this.user)
    },
    /**
     * Font size of small avatars should be half the size,
     * bigger avatars should have more visual padding
     */
    fontSize() {
      const ratio = range(16, 32, 2, 2.3, Number(this.size))
      return Number(this.size) / ratio
    },
    style() {
      return {
        color: contrastColor(this.color),
        fontSize: `${this.fontSize}px`,
        fontWeight: Number(this.size) > 44 ? 450 : Number(this.size) > 24 ? 500 : 600,
        letterSpacing: Number(this.size) < 24 ? '.05em' : 0,
        marginRight: Number(this.size) < 24 ? '-.125em' : 0,
      }
    },
  },
}
</script>
