<template>
  <NavDesktop v-if="$vuetify.breakpoint.smAndUp" :camp="camp" />
  <div v-else>
    <NavMobileBottom :camp="camp" @input="open = $event" />
    <NavMobileSidebar :camp="camp" v-model="open" />
  </div>
</template>

<script>
import NavDesktop from '@/views/camp/navigation/NavDesktop.vue'
import NavMobileBottom from '@/views/camp/navigation/mobile/NavMobileBottom.vue'
import NavMobileSidebar from '@/views/camp/navigation/mobile/NavMobileSidebar.vue'

export default {
  name: 'CampNavigation',
  components: {
    NavMobileSidebar,
    NavMobileBottom,
    NavDesktop,
  },
  data() {
    return {
      open: false,
    }
  },
  props: {
    camp: { type: Function, required: true },
  },
}
</script>

<style lang="scss" scoped>
.camp--name::v-deep .v-btn__content {
  width: 100%;
}

.v-bottom-navigation--fixed {
  height: auto !important;
  min-height: 56px;
  padding-bottom: env(safe-area-inset-bottom);
}

.v-application .ec-close-drawer {
  background-color: #{map-get($blue-grey, 'lighten-5')};
  border-top: 1px solid #{map-get($blue-grey, 'lighten-4')};
}
</style>
