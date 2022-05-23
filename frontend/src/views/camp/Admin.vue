<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('views.camp.admin.title')">
    <v-card-text>
      <v-btn block :to="{ name: 'profile', query: {isDetail: true}}" class="d-sm-none mb-2">
        {{ $tc('views.profile.profile') }}
      </v-btn>
      <v-btn block :to="{ name: 'camps', query: {isDetail: true}}" class="d-sm-none">
        {{ $tc('views.camps.title') }}
      </v-btn>
      <v-row>
        <v-col cols="12" lg="6">
          <camp-settings :camp="camp" :disabled="!isManager" />
          <camp-address :camp="camp" :disabled="!isManager" />

          <v-btn v-if="$vuetify.breakpoint.xsOnly" :to="{name: 'camp/collaborators', query: {isDetail: true}}">
            {{ $tc('views.camp.admin.collaborators') }}
          </v-btn>
          <camp-periods :camp="camp" :disabled="!isManager" />
        </v-col>
        <v-col cols="12" lg="6">
          <camp-categories :camp="camp" :disabled="!isManager" />

          <camp-material-lists :camp="camp" :disabled="!isManager" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" lg="6">
          <camp-danger-zone v-if="isManager" :camp="camp" />
        </v-col>
      </v-row>
    </v-card-text>
  </content-card>
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

export default {
  name: 'Admin',
  components: {
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
    camp: { type: Function, required: true }
  },
  data () {
    return {}
  },
  mounted () {
    this.api.reload(this.camp())
    this.api.reload(this.camp().materialLists())
  }
}
</script>

<style scoped>
</style>
