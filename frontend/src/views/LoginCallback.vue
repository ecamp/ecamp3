<template>
  <section class="container">
    <h3>Logging you in...</h3>
  </section>
</template>

<script>
export default {
  name: 'LoginCallback',
  beforeRouteEnter (to, from, next) {
    const token = window.location.search.replace('?token=', '')
    window.opener.loginSuccess(token)
    window.close()
  }
}
</script>

<style scoped>

</style>
