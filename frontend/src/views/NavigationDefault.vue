<template>
  <v-app-bar
    v-if="$vuetify.breakpoint.smAndUp"
    app
    clipped-left
    color="blue-grey darken-4"
    dark
  >
    <logo text />
    <v-spacer />
    <user-meta />
  </v-app-bar>
</template>

<script>
import UserMeta from '@/components/navigation/UserMeta.vue'
import Logo from '@/components/navigation/Logo.vue'

export default {
  name: 'NavigationDefault',
  components: {
    UserMeta,
    Logo,
  },
  data() {
    return {
      logoutIcon: 'mdi-logout',
    }
  },
  computed: {
    isLoggedIn() {
      return this.$auth.isLoggedIn()
    },
  },
  methods: {
    logout() {
      this.logoutIcon = ''
      this.$auth.logout().then(() => this.$router.replace({ name: 'login' }))
    },
    prevent(event) {
      event.stopImmediatePropagation()
      event.preventDefault()
      event.cancelBubble = true
      return null
    },
  },
}
</script>

<style scoped></style>
