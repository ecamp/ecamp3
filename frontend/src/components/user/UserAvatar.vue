<template>
  <v-avatar :size="size" :color="color">
    <span class="white--text" :style="style">{{ initials }}</span>
  </v-avatar>
</template>
<script>
import campCollaborationInitials from '@/common/helpers/campCollaborationInitials.js'
import campCollaborationColor from '@/common/helpers/campCollaborationColor.js'
import userColor from '@/common/helpers/userColor.js'
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
      return (this.user || this.campCollaboration)._meta.loading
    },
    color() {
      if (this.isLoading) {
        return 'rgba(0,0,0,0)'
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
    style() {
      return {
        fontSize: this.size / 2.5 + 'px',
        fontWeight: this.size >= 40 ? 400 : 600,
        letterSpacing: '.1em',
        marginRight: '-.125em',
      }
    },
  },
}
</script>
