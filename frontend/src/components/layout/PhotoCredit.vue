<template>
  <a v-if="$vuetify.breakpoint.smAndUp"
     class="photo_credits" target="_blank">
    <slot />
  </a>
</template>

<script>
export default {
  name: 'PhotoCredit'
}
</script>

<style lang="scss" scoped>
  .photo_credits {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: #000;
    color: #fff;
    font-size: 10px;
    padding: 4px;
    text-decoration: none;
  }
</style>
