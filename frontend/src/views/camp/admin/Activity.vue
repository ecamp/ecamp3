<template>
  <content-card :title="$tc('views.camp.admin.activity.title')" toolbar>
    <v-container fluid class="px-4 pb-8">
      <v-row>
        <v-col cols="12" lg="6" class="pb-0 pr-lg-6">
          <CampCategories :camp="camp" :disabled="!isManager" />
        </v-col>
        <v-col cols="12" lg="6" class="pb-0 pr-lg-6">
          <CampActivityProgressLabels :camp="camp" :disabled="!isManager" />
        </v-col>
      </v-row>
    </v-container>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import CampCategories from '@/components/campAdmin/CampCategories.vue'
import CampActivityProgressLabels from '@/components/campAdmin/CampActivityProgressLabels.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'

export default {
  name: 'CampAdminActivity',
  components: {
    CampActivityProgressLabels,
    CampCategories,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Object, required: true },
  },
}
</script>
