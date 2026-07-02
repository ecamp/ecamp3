<template>
  <v-container fluid>
    <content-card :title="$t('views.camp.material.materialOverview.title')" toolbar>
      <template #title-actions>
        <v-menu offset-y>
          <template #activator="{ props }">
            <v-btn icon v-bind="props">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list class="py-0">
            <DialogMaterialListCreate v-if="isContributor" :camp="camp">
              <template #activator="{ props }">
                <v-list-item v-bind="props">
                  <template #prepend>
                    <v-icon>mdi-plus</v-icon>
                  </template>
                  {{ $t('views.camp.material.materialOverview.createNewList') }}
                </v-list-item>
              </template>
            </DialogMaterialListCreate>
            <v-list-item :disabled="isDownloadingXlsx" @click.stop="downloadXlsx">
              <template #prepend>
                <v-progress-circular
                  v-if="isDownloadingXlsx"
                  class="mr-2"
                  indeterminate
                  size="24"
                  width="2"
                />
                <v-icon v-else>mdi-microsoft-excel</v-icon>
              </template>
              {{ $t('views.camp.material.materialOverview.download') }}
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
      <v-expansion-panels
        v-if="collection.length > 1"
        v-model="openPeriods"
        multiple
        flat
        variant="accordion"
      >
        <PeriodMaterialLists
          v-for="{ period, materialItems } in collection"
          :key="period._meta.self"
          :period="period"
          :material-item-collection="materialItems"
        />
      </v-expansion-panels>
      <v-card-text v-else-if="collection.length === 1">
        <MaterialTable
          v-for="{ period, materialItems } in collection"
          :key="period._meta.self"
          :camp="camp"
          :material-item-collection="materialItems"
          :period="period"
          :disabled="!isContributor"
        />
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import MaterialTable from '@/components/material/MaterialTable.vue'
import PeriodMaterialLists from '@/components/material/PeriodMaterialLists.vue'
import DialogMaterialListCreate from '@/components/campAdmin/DialogMaterialListCreate.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import { useMaterialViewHelper } from '@/components/material/useMaterialViewHelper.js'

export default {
  name: 'MaterialOverview',
  components: {
    ContentCard,
    DialogMaterialListCreate,
    MaterialTable,
    PeriodMaterialLists,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Object, required: true },
  },
  setup(props) {
    return useMaterialViewHelper(props.camp)
  },
  head() {
    return {
      title: this.$t('views.camp.material.materialOverview.title'),
    }
  },
}
</script>
