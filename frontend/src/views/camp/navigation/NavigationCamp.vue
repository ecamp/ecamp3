<template>
  <NavTopbar v-if="$vuetify.breakpoint.mdAndUp" :camp="camp" />
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

<style lang="scss" scoped></style>
