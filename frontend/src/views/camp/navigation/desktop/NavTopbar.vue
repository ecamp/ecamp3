<template>
  <v-app-bar app clipped-left color="blue-grey darken-4" dark>
    <logo text />

    <v-toolbar-items>
      <v-btn :to="campRoute(camp())" text>
        <v-icon :left="$vuetify.display.mdAndUp">mdi-tent</v-icon>
        <span class="sr-only-sm-and-down">{{ camp().title }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'program')" text>
        <v-icon :left="$vuetify.display.mdAndUp">mdi-view-dashboard</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigation.desktop.navTopbar.program')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'story')" text>
        <v-icon :left="$vuetify.display.mdAndUp"> mdi-book-open-variant </v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigation.desktop.navTopbar.story')
        }}</span>
      </v-btn>
      <v-btn :to="materialListRoute(camp())" text>
        <v-icon :left="$vuetify.display.mdAndUp"> mdi-package-variant </v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigation.desktop.navTopbar.material')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'admin')" text>
        <v-icon :left="$vuetify.display.mdAndUp"> mdi-cogs </v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('global.navigation.admin.title')
        }}</span>
      </v-btn>
    </v-toolbar-items>
    <v-spacer />
    <user-meta />
  </v-app-bar>
</template>

<script>
import UserMeta from '@/components/navigation/UserMeta.vue'
import Logo from '@/components/navigation/Logo.vue'
import { campRoute, materialListRoute } from '@/router.js'
import { mapGetters } from 'vuex'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'

export default {
  name: 'NavTopbar',
  components: {
    UserMeta,
    Logo,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Function, required: true },
  },
  data() {
    return {
      open: false,
    }
  },
  computed: {
    ...mapGetters({
      user: 'getLoggedInUser',
    }),
  },
  methods: {
    materialListRoute,
    campRoute,
  },
}
</script>

<style lang="scss" scoped>
.camp--name:deep(.v-btn__content) {
  width: 100%;
}
</style>
