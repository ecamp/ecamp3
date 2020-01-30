<template>
  <v-card>
    <v-toolbar dense color="blue-grey lighten-5">
      <v-toolbar-title>
        Logout
      </v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <p v-if="!error">
        Logging you out...
      </p>
      <v-alert v-else type="error">
        Error logging out.
      </v-alert>
    </v-card-text>
  </v-card>
</template>
<script>
export default {
  name: 'Logout',
  data () {
    return {
      error: false
    }
  },
  beforeRouteEnter (to, from, next) {
    next(vm => vm.logout())
  },
  methods: {
    logout () {
      this.$auth.logout(this.redirect)
    },
    redirect () {
      this.$router.replace(this.$route.query.redirect || '/')
    }
  }
}
</script>
<style scoped>
</style>
