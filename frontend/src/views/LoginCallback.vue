<template>
  <section class="container">
    <h3>Logging you in...</h3>
  </section>
</template>
<script>
export default {
  name: 'LoginCallback',
  beforeRouteEnter (to, from, next) {
    window.opener.loginSuccess()
    window.close()
  }
}
</script>
<style scoped>
</style>
