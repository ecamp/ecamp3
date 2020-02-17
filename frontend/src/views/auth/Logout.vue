<template>
  <v-card>
    <v-toolbar dense color="blue-grey lighten-5">
      <v-toolbar-title>
        Logout
      </v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <p>
        Logging you out...
      </p>
    </v-card-text>
  </v-card>
</template>
<script>
export default {
  name: 'Logout',
  beforeRouteEnter (to, from, next) {
    next(vm => vm.logout())
  },
  methods: {
    logout () {
      this.$auth.logout(this.redirect)
    },
    redirect () {
      this.$router.replace({ name: 'login' })
    }
  }
}
</script>
<style scoped>
</style>
