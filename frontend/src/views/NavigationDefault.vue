<template>
  <v-app-bar
    v-if="$vuetify.breakpoint.smAndUp"
    app
    clipped-left
    color="blue-grey darken-4"
    dark
  >
    <logo text />
    <v-spacer />
    <user-meta />
  </v-app-bar>
</template>

<script>
import UserMeta from '@/components/navigation/UserMeta.vue'
import Logo from '@/components/navigation/Logo.vue'

export default {
  name: 'NavigationDefault',
  components: {
    UserMeta,
    Logo,
  },
}
</script>
