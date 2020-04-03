<template>
  <transition name="fade">
    <div
      v-if="visible"
      class="out-of-flow-parent">
      <v-avatar
        color="white"
        class="out-of-flow-child"
        size="32">
        <v-icon
          color="green"
          size="24">
          mdi-check
        </v-icon>
      </v-avatar>
    </div>
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
  transition: opacity 1s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

.out-of-flow-parent{
  position: relative;
  top:-4px;
  left:-28px;
  width:0px;
  height:0px;
}

.out-of-flow-child{
  position: absolute;
}
</style>
