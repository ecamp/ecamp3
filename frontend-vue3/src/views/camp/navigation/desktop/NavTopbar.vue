<template>
  <v-app-bar app clipped-left color="blue-grey darken-4" dark>
    <logo text />

    <v-toolbar-items>
      <v-btn :to="campRoute(camp)" text>
        <v-icon :left="$vuetify.display.mdAndUp">mdi-tent</v-icon>
        <span class="sr-only-sm-and-down">{{
          camp.title
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp, 'program')" text>
        <v-icon :left="$vuetify.display.mdAndUp">mdi-view-dashboard</v-icon>
        <span class="sr-only-sm-and-down">{{
          $t('views.camp.navigation.desktop.navTopbar.program')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp, 'story')" text>
        <v-icon :left="$vuetify.display.mdAndUp"> mdi-book-open-variant </v-icon>
        <span class="sr-only-sm-and-down">{{
          $t('views.camp.navigation.desktop.navTopbar.story')
        }}</span>
      </v-btn>
      <v-btn :to="materialListRoute(camp)" text>
        <v-icon :left="$vuetify.display.mdAndUp"> mdi-package-variant </v-icon>
        <span class="sr-only-sm-and-down">{{
          $t('views.camp.navigation.desktop.navTopbar.material')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp, 'admin')" text>
        <v-icon :left="$vuetify.display.mdAndUp"> mdi-cogs </v-icon>
        <span class="sr-only-sm-and-down">{{ $t('global.navigation.admin.title') }}</span>
      </v-btn>
    </v-toolbar-items>
    <v-spacer />
    <v-toolbar-items v-if="$vuetify.display.lgAndUp">
      <v-btn :href="helpLink" target="_blank" text>
        {{ $t('global.navigation.help') }}
        <span class="blue-grey--text"><v-icon small right>mdi-open-in-new</v-icon></span>
      </v-btn>
    </v-toolbar-items>
    <UserMeta :camp="camp" />
  </v-app-bar>
</template>

<script>
import UserMeta from '@/components/navigation/UserMeta.vue'
import Logo from '@/components/navigation/Logo.vue'
import { campRoute, materialListRoute } from '@/router.js'
import { mapGetters } from 'vuex'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import { getEnv } from '@/environment.js'

export default {
  name: 'NavTopbar',
  components: {
    UserMeta,
    Logo,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      open: false,
    }
  },
  computed: {
    helpLink() {
      return getEnv().HELP_LINK
    },
    loading() {
      return true
    },
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
