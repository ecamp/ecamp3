<template>
  <v-avatar :size="size" :color="color">
    <span class="white--text" :style="style">{{ initials }}</span>
  </v-avatar>
</template>
<script>

export default {
  name: 'UserAvatar',
  props: {
    size: { type: Number, required: false, default: 48 },
    value: { type: Object, required: true }
  },
  computed: {
    user () {
      if (this.value.user instanceof Function) {
        return this.value.user()
      }
      if (this.value.user instanceof Object) {
        return this.value.user
      }
      return this.value
    },
    color () {
      const id = this.user.id
      const h = (parseInt(id, 16) % 360)
      return `hsl(${h}, 100%, 40%)`
    },
    initials () {
      let displayName = ''
      if (this.user.displayName) {
        displayName = this.user.displayName
      } else if (this.value.inviteEmail) {
        displayName = this.value.inviteEmail.split('@', 2).shift()
      }
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
        fontSize: (this.size / 2.5) + 'px'
      }
    }
  }
}

</script>
