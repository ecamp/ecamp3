<template>
  <v-toolbar-items>
    <v-btn text class="px-2"
           min-width="0" rounded
           exact :to="{ name: 'home'}">
      <v-toolbar-title>
        <v-icon>$vuetify.icons.ecamp</v-icon>️<span v-if="text" class="ml-4 mr-2">eCamp</span>
      </v-toolbar-title>
    </v-btn>
    <slot />
  </v-toolbar-items>
</template>

<script>
import { campRoute } from '@/router.js'

export default {
  name: 'Logo',
  props: {
    text: { type: Boolean, default: false }
  },
  computed: {
    campHome () {
      return this.camp ? campRoute(this.camp, 'program') : false
    }
  }
}
</script>

<style scoped>

</style>
