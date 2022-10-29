<template>
  <div class="avatar" :style="avatarStyle">
    <span class="initials" :style="style">{{ initials }}</span>
  </div>
</template>
<script>
import {
  campCollaborationColor,
  defaultColor,
  userColor,
} from '@/../common/helpers/colors.js'
import userInitials from '@/../common/helpers/userInitials.js'
import campCollaborationInitials from '@/../common/helpers/campCollaborationInitials.js'

export default {
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
        return defaultColor()
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
    avatarStyle() {
      return {
        width: this.size + 'px',
        height: this.size + 'px',
        backgroundColor: this.color.toString({ format: 'hex' }),
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
      }
    },
    style() {
      return {
        fontSize: this.size / 2.5 + 'px',
      }
    },
  },
}
</script>

<style scoped lang="scss">
.avatar {
  border-radius: 9999px;
}

.initials {
  color: white;
  font-weight: 600;
}
</style>
