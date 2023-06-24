<!--
Displays several tabs with details on a single camp.
-->

<template>
  <v-container fluid>
    <content-card
      :title="$tc('views.material.material.title', 0, { name: 'gigu' })"
      toolbar
    >
      <template #title-actions>
        <v-btn small color="primary" class="ml-5" @click="downloadXlsx">
          <v-icon left>mdi-microsoft-excel</v-icon>
          {{ $tc('views.camp.material.downloadXlsx') }}
        </v-btn>
      </template>
      <v-card-text>
        <e-switch
          v-model="showActivityMaterial"
          :label="$tc('views.camp.material.showActivityMaterial')"
        />
      </v-card-text>
      <v-expansion-panels v-model="openPeriods" multiple flat accordion>
        <period-material-lists
          v-for="period in camp().periods().items"
          :key="period._meta.self"
          :period="period"
          :show-activity-material="showActivityMaterial"
          :disabled="!isContributor"
        />
      </v-expansion-panels>
    </content-card>
  </v-container>
</template>

<script>
import CampMaterial from '@/views/camp/Material.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import PeriodMaterialLists from '@/components/material/PeriodMaterialLists.vue'

export default {
  name: 'MaterialList',
  components: { PeriodMaterialLists, ContentCard, CampMaterial },
  props: {
    camp: { type: Function, required: true },
  },
}
</script>
