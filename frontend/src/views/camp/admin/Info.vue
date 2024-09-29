<template>
  <content-card :title="$tc('views.camp.admin.info.title')" toolbar>
    <v-container fluid class="px-4 pb-8">
      <v-row>
        <v-col cols="12" md="6" class="pb-0">
          <CampSettings :camp="camp" :disabled="!isManager" />
        </v-col>
        <v-col cols="12" md="6" class="pb-0">
          <CampPeriods :camp="camp" :disabled="!isManager" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="6" class="pb-0">
          <CampAddress :camp="camp" :disabled="!isManager" />
        </v-col>
      </v-row>
    </v-container>
    <v-expansion-panels v-model="openPanels" flat accordion multiple>
      <CampConditionalFields :camp="camp" :disabled="!isManager" />
      <CampDangerZone v-if="isManager" :camp="camp" />
    </v-expansion-panels>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import CampAddress from '@/components/campAdmin/CampAddress.vue'
import CampSettings from '@/components/campAdmin/CampSettings.vue'
import CampConditionalFields from '@/components/campAdmin/CampConditionalFields.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import CampPeriods from '@/components/campAdmin/CampPeriods.vue'
import CampDangerZone from '@/components/campAdmin/CampDangerZone.vue'

export default {
  name: 'CampAdminInfo',
  components: {
    CampDangerZone,
    CampPeriods,
    CampConditionalFields,
    CampSettings,
    CampAddress,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Object, required: true },
  },
  data: () => ({
    openPanels: [],
  }),
  mounted() {
    this.camp._meta.load.then((camp) => {
      if (
        camp?.organizer ||
        camp?.kind ||
        camp?.coachName ||
        camp?.courseNumber ||
        camp?.courseKind ||
        camp?.trainingAdvisorName
      ) {
        this.openPanels.push(0)
      }
    })
  },
}
</script>
