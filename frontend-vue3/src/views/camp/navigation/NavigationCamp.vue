<template>
  <NavTopbar v-if="$vuetify.display.mdAndUp" :camp="camp" />
  <div v-else>
    <NavBottombar :camp="camp" @input="open = $event" />
    <NavSidebar v-model="open" :camp="camp" />
  </div>
</template>

<script>
import NavTopbar from '@/views/camp/navigation/desktop/NavTopbar.vue'
import NavBottombar from '@/views/camp/navigation/mobile/NavBottombar.vue'
import NavSidebar from '@/views/camp/navigation/mobile/NavSidebar.vue'

export default {
  name: 'NavigationCamp',
  components: {
    NavTopbar,
    NavBottombar,
    NavSidebar,
  },
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      open: false,
    }
  },
}
</script>

<style lang="scss" scoped>
@use 'vuetify/settings';
@use "sass:map";

.camp--name:deep(.v-btn__content) {
  width: 100%;
}

.v-bottom-navigation--fixed {
  height: auto !important;
  min-height: 56px;
  padding-bottom: env(safe-area-inset-bottom);
}

.v-application .ec-close-drawer {
  background-color: #{map.get(settings.$blue-grey, 'lighten-5')};
  border-top: 1px solid #{map.get(settings.$blue-grey, 'lighten-4')};
}
</style>
