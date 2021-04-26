<template>
  <v-avatar :size="size" :color="color">
    <span class="white--text" :style="style">{{ initialen }}</span>
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
      const h = (parseInt(id, 16) % 256) / 256
      return this.hsvToRgb(h, 1, 0.6)
    },
    initialen () {
      if (this.user.displayName) {
        const items = this.user.displayName.split(/[ ,._-]/)
        if (items.length === 1) {
          return this.user.displayName.substr(0, 2)
        } else {
          return items[0].substr(0, 1) + items[1].substr(0, 1)
        }
      } else if (this.value.inviteEmail) {
        const localPart = this.value.inviteEmail.split('@').shift()
        const items = localPart.split(/[ ,._-]/)
        if (items.length === 1) {
          return this.value.inviteEmail.substr(0, 2)
        } else {
          return items[0].substr(0, 1) + items[1].substr(0, 1)
        }
      }
      return ''
    },
    style () {
      return {
        fontSize: (this.size / 2.5) + 'px'
      }
    }
  },
  methods: {
    hsvToRgb (h, s, v) {
      let r, g, b

      const i = Math.floor(h * 6)
      const f = h * 6 - i
      const p = v * (1 - s)
      const q = v * (1 - f * s)
      const t = v * (1 - (1 - f) * s)

      switch (i % 6) {
        case 0:
          r = Math.floor(v * 255)
          g = Math.floor(t * 255)
          b = Math.floor(p * 255)
          break
        case 1:
          r = Math.floor(q * 255)
          g = Math.floor(v * 255)
          b = Math.floor(p * 255)
          break
        case 2:
          r = Math.floor(p * 255)
          g = Math.floor(v * 255)
          b = Math.floor(t * 255)
          break
        case 3:
          r = Math.floor(p * 255)
          g = Math.floor(q * 255)
          b = Math.floor(v * 255)
          break
        case 4:
          r = Math.floor(t * 255)
          g = Math.floor(p * 255)
          b = Math.floor(v * 255)
          break
        case 5:
          r = Math.floor(v * 255)
          g = Math.floor(p * 255)
          b = Math.floor(q * 255)
          break
      }

      return '#' +
        r.toString(16).padStart(2, '0') +
        g.toString(16).padStart(2, '0') +
        b.toString(16).padStart(2, '0')
    }
  }
}

</script>
