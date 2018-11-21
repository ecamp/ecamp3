<template>
  <section class="container">
    <h3>Logout</h3>
    <p v-if="!error">Logging you out...</p>
    <p v-else>Error logging out.</p>
  </section>
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
      this.$auth.logout()
      this.redirect()
    },
    redirect () {
      this.$router.replace(this.$route.query.redirect || '/')
    }
  }
}
</script>
<style scoped>
</style>
