<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <div style="display: contents">
    <content-card v-if="open === 'overview'"
                  :title="$tc('views.camp.admin.mobile.title')">
      <v-card-text class="blue-grey lighten-5 fill-height">
        <v-list class="rounded-lg pb-0 mb-3">
          <v-list-item two-line link
                       class="pb-1"
                       @click="open = 'profile'">
            <UserAvatar :user="user" class="mr-2" />
            <v-list-item-content>
              <v-list-item-title>{{ user.displayName }}</v-list-item-title>
              <v-list-item-subtitle>{{ user.profile().firstname + ' ' + user.profile().surname }}</v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-action>
              <v-icon>mdi-chevron-right</v-icon>
            </v-list-item-action>
          </v-list-item>
          <v-divider />
          <v-list-item link exact @click="open = 'mycamps'">
            <v-list-item-content>
              <v-list-item-title>Meine Lager</v-list-item-title>
            </v-list-item-content>
            <v-list-item-action>
              <v-icon>mdi-chevron-right</v-icon>
            </v-list-item-action>
          </v-list-item>
        </v-list>
        <v-list class="rounded-lg mt-2 py-0">
          <v-list-item two-line>
            <v-list-item-content>
              <v-list-item-title>{{ camp().name }}</v-list-item-title>
              <v-list-item-subtitle>{{ camp().motto }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-divider inset i />
          <SettingListItem title="Campinfos" icon="mdi-information" @click="open = 'campinfos'" />
          <v-divider inset />
          <SettingListItem title="Team" icon="mdi-account-group"
                           :to="{name: 'camp/collaborators', query: {isDetail: true}}" />
          <v-divider inset />
          <SettingListItem title="Datum & Zeit" icon="mdi-calendar" @click="open = 'date'" />
          <v-divider inset />
          <SettingListItem title="Aktivitätskategorien" icon="mdi-tag" @click="open = 'categories'" />
          <v-divider inset />
          <SettingListItem title="Materiallisten" icon="mdi-package-variant" @click="open = 'material'" />
          <v-divider inset />
          <SettingListItem title="Lager löschen" icon="mdi-bomb" @click="open = 'delete'" />
        </v-list>
      </v-card-text>
    </content-card>
    <content-card v-if="open === 'profile'"
                  :title="$tc('views.profile.profile')"
                  :back="backToOverview">
      <v-card-text>
        <ProfileForm />
        <v-btn color="red"
               class="mt-4"
               type="button"
               block
               outlined
               large
               dark
               @click="$auth.logout()">
          {{ $tc('global.button.logout') }}
        </v-btn>
      </v-card-text>
    </content-card>
    <content-card v-if="open === 'mycamps'"
                  :title="$tc('views.home.myCamps')"
                  :back="backToOverview">
      <MyCamps />
    </content-card>
    <content-card v-if="open === 'campinfos'"
                  title="Campinfos"
                  :back="backToOverview">
      <v-card-text>
        <camp-settings :camp="camp" :disabled="!isManager" />
        <camp-address :camp="camp" :disabled="!isManager" class="mt-8" />
      </v-card-text>
    </content-card>
    <content-card v-if="open === 'date'"
                  :title="$tc('components.camp.campPeriods.title')"
                  :back="backToOverview">
      <camp-periods :camp="camp" :disabled="!isManager" />
      <dialog-period-create v-if="!disabled" :camp="camp()">
        <template #activator="{ on }">
          <v-btn
            class="mb-16"
            fixed
            dark
            fab
            bottom
            right
            color="red"
            v-on="on">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </template>
      </dialog-period-create>
    </content-card>
    <content-card v-if="open === 'categories'"
                  :title="$tc('components.camp.campCategories.title')"
                  :back="backToOverview">
      <v-card-text>
        <camp-categories :camp="camp" :disabled="!isManager" />
      </v-card-text>
    </content-card>
    <content-card v-if="open === 'material'"
                  :title="$tc('components.camp.campMaterialLists.title')"
                  :back="backToOverview" class="relative">
      <dialog-material-list-create v-if="isManager" :camp="camp()">
        <template #activator="{ on }">
          <v-btn
            class="mb-16"
            fixed
            dark
            fab
            bottom
            right
            color="red"
            v-on="on">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </template>
      </dialog-material-list-create>
      <camp-material-lists :camp="camp" :disabled="!isManager" />
    </content-card>
    <content-card v-if="open === 'delete'"
                  :title="$tc('components.camp.campDangerzone.title')"
                  :back="backToOverview">
      <v-card-text>
        <camp-danger-zone v-if="isManager" :camp="camp" />
      </v-card-text>
    </content-card>
  </div>
</template>

<script>
import CampSettings from '@/components/campAdmin/CampSettings.vue'
import CampAddress from '@/components/campAdmin/CampAddress.vue'
import CampPeriods from '@/components/campAdmin/CampPeriods.vue'
import CampMaterialLists from '@/components/campAdmin/CampMaterialLists.vue'
import CampCategories from '@/components/campAdmin/CampCategories.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import CampDangerZone from '@/components/campAdmin/CampDangerZone.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import UserAvatar from '@/components/user/UserAvatar.vue'
import ProfileForm from '@/components/profile/ProfileForm.vue'
import SettingListItem from '@/components/campAdmin/SettingListItem.vue'
import DialogPeriodCreate from '@/components/campAdmin/DialogPeriodCreate.vue'
import DialogMaterialListCreate from '@/components/campAdmin/DialogMaterialListCreate.vue'
import MyCamps from '@/components/MyCamps.vue'

export default {
  name: 'MobileAdmin',
  components: {
    MyCamps,
    DialogMaterialListCreate,
    DialogPeriodCreate,
    SettingListItem,
    ProfileForm,
    UserAvatar,
    CampDangerZone,
    ContentCard,
    CampSettings,
    CampAddress,
    CampPeriods,
    CampMaterialLists,
    CampCategories
  },
  mixins: [campRoleMixin],
  props: {
    camp: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      open: 'overview'
    }
  },
  computed: {
    user () {
      return this.$auth.user()
    }
  },
  mounted () {
    this.api.reload(this.camp())
    this.api.reload(this.camp().materialLists())
  },
  methods: {
    backToOverview () {
      this.open = 'overview'
    }
  }
}
</script>

<style scoped>
</style>
