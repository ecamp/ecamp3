<template>
  <transition name="fade">
    <v-icon v-if="visible"
            color="green">
      mdi-check
    </v-icon>
  </transition>
</template>

<script>
export default {
  name: 'SuccessIcon',
  props: {
    visible: {
      type: Boolean,
      default: true
    }
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>
