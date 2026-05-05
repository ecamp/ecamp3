<template>
  <v-app-bar app clipped-left color="blue-grey-darken-4" dark>
    <logo text />

    <v-toolbar-items>
      <TopNavigationItem
        :to="campRoute(camp)"
        icon="mdi-tent"
        :text="campShortTitle(camp)"
      />
      <TopNavigationItem
        :to="campRoute(camp, 'program')"
        icon="mdi-view-dashboard"
        :text="$t('views.camp.navigation.desktop.navTopbar.program')"
      />
      <TopNavigationItem
        v-if="camp.isCourse"
        :to="campRoute(camp, 'overview/checklists')"
        icon="mdi-clipboard-list-outline"
        :text="$t('views.camp.navigation.desktop.navTopbar.checklist')"
      />
      <TopNavigationItem
        :to="campRoute(camp, 'story')"
        icon="mdi-book-open-variant"
        :text="$t('views.camp.navigation.desktop.navTopbar.story')"
      />
      <TopNavigationItem
        :to="materialListRoute(camp)"
        icon="mdi-package-variant"
        :text="$t('views.camp.navigation.desktop.navTopbar.material')"
      />
      <TopNavigationItem
        :to="campRoute(camp, 'admin')"
        icon="mdi-cogs"
        :text="$t('global.navigation.admin.title')"
      />
    </v-toolbar-items>
    <v-spacer />
    <v-toolbar-items v-if="$vuetify.display.lgAndUp">
      <v-btn :href="helpLink" target="_blank" variant="text">
        {{ $t('global.navigation.help') }}
        <v-icon size="small" end color="blue-grey">mdi-open-in-new</v-icon>
      </v-btn>
    </v-toolbar-items>
    <UserMeta :camp="isOutsider ? null : camp" />
  </v-app-bar>
</template>

<script>
import UserMeta from '@/components/navigation/UserMeta.vue'
import Logo from '@/components/navigation/Logo.vue'
import { campRoute, materialListRoute } from '@/router.js'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import { getEnv } from '@/environment.js'
import campShortTitle from '@/common/helpers/campShortTitle.js'
import TopNavigationItem from '@/components/navigation/TopNavigationItem.vue'

export default {
  name: 'NavTopbar',
  components: {
    TopNavigationItem,
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
  },
  methods: {
    materialListRoute,
    campRoute,
    campShortTitle,
  },
}
</script>

<style lang="scss" scoped></style>
