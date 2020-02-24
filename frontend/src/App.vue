<template>
  <div id="app">
    <component :is="layout">
      <router-view />
    </component>
  </div>
</template>

<script>
const defaultLayout = 'default'

export default {
  name: 'App',
  computed: {
    layout () {
      return (this.$route.meta.layout || defaultLayout) + '-layout'
    }
  }
}
</script>
<style lang="scss">
  @import "src/scss/global";
  // consider replacing with CDN for production
  @import '../node_modules/@mdi/font/css/materialdesignicons.css';
</style>
