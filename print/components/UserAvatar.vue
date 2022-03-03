<template>
  <div class="avatar" :style="avatarStyle">
    <span class="initials" :style="style">{{ initials }}</span>
  </div>
</template>
<script>
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
    objectId() {
      if (this.isLoading) return null
      if (this.user) {
        return this.user.id
      }
      if (this.campCollaboration) {
        if (typeof this.campCollaboration.user === 'function') {
          return this.campCollaboration.user().id
        }
        return this.campCollaboration.id
      }
      return undefined
    },
    objectText() {
      if (this.isLoading) {
        return ''
      }
      if (this.user) {
        return this.user.displayName
      }
      if (this.campCollaboration) {
        if (typeof this.campCollaboration.user === 'function') {
          return this.campCollaboration.user().displayName
        }
        return this.campCollaboration.inviteEmail.split('@', 2).shift()
      }
      return ''
    },
    color() {
      if (!this.isLoading) {
        const h = parseInt(this.objectId, 16) % 360
        return `hsl(${h}, 100%, 30%)`
      } else {
        return 'rgba(0, 0, 0, 0)'
      }
    },
    initials() {
      const displayName = this.objectText
      let items = displayName.split(' ', 2)
      if (items.length === 1) {
        items = items.shift().split(/[,._-]/, 2)
      }
      if (items.length === 1) {
        return displayName.substr(0, 2)
      } else {
        return items[0].substr(0, 1) + items[1].substr(0, 1)
      }
    },
    avatarStyle() {
      return {
        width: this.size + 'px',
        height: this.size + 'px',
        backgroundColor: this.color,
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
