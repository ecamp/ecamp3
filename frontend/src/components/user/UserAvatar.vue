<template>
  <v-avatar :size="size" :color="color">
    <span class="white--text font-weight-medium" :style="style">{{ initials }}</span>
  </v-avatar>
</template>
<script>

export default {
  name: 'UserAvatar',
  props: {
    size: { type: [String, Number], required: false, default: 48 },
    user: { type: Object, default: null },
    campCollaboration: { type: Object, default: null }
  },
  computed: {
    isLoading () {
      return (this.user || this.campCollaboration)._meta.loading
    },
    objectId () {
      if (this.isLoading) return null
      if (this.user) { return this.user.id }
      if (this.campCollaboration) {
        if (typeof this.campCollaboration.user === 'function') {
          return this.campCollaboration.user().id
        }
        return this.campCollaboration.id
      }
      return undefined
    },
    objectText () {
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
    color () {
      if (!this.isLoading) {
        const h = (parseInt(this.objectId, 16) % 360)
        return `hsl(${h}, 100%, 30%)`
      } else {
        return 'rgba(0, 0, 0, 0)'
      }
    },
    initials () {
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
    style () {
      return {
        fontSize: (Number(this.size) / 2.5) + 'px',
        fontWeight: 400,
        letterSpacing: '1px',
        marginRight: '-1.5px'
      }
    }
  }
}

</script>
