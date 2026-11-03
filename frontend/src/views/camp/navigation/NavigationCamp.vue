<template>
  <NavTopbar v-if="$vuetify.display.mdAndUp" :camp="camp" />
  <div v-else>
    <NavSidebar v-model="open" :camp="camp" />
    <NavBottombar :camp="camp" @input="open = $event" />
  </div>
</template>

<script>
import NavTopbar from '@/views/camp/navigation/desktop/NavTopbar.vue'
import NavBottombar from '@/views/camp/navigation/mobile/NavBottombar.vue'
import NavSidebar from '@/views/camp/navigation/mobile/NavSidebar.vue'
import { campShortTitle } from '@/helpers/campShortTitle'

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
  head() {
    return {
      templateParams: {
        section: () => campShortTitle(this.camp),
      },
    }
  },
}
</script>

<style lang="scss" scoped></style>
