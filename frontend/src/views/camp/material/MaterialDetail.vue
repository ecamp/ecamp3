<template>
  <v-container fluid>
    <content-card :title="materialList.name" toolbar>
      <template #title-actions>
        <v-menu offset-y>
          <template #activator="{ props }">
            <v-btn icon v-bind="props">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list class="py-0">
            <DialogMaterialListEdit v-if="isContributor" :material-list="materialList">
              <template #activator="{ props }">
                <v-list-item v-bind="props">
                  <template #prepend>
                    <v-icon>mdi-pencil</v-icon>
                  </template>
                  {{ $t('global.button.edit') }}
                </v-list-item>
              </template>
            </DialogMaterialListEdit>
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
              {{ $t('global.button.download') }}
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
          :material-list="materialList"
          :disabled="!isContributor"
        />
      </v-expansion-panels>
      <v-card-text v-else-if="collection.length === 1">
        <MaterialTable
          v-for="{ period, materialItems } in collection"
          :key="period._meta.self"
          :camp="camp"
          :material-item-collection="materialItems"
          :period="period"
          :material-list="materialList"
          :disabled="!isContributor"
        />
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import PeriodMaterialLists from '@/components/material/PeriodMaterialLists.vue'
import MaterialTable from '@/components/material/MaterialTable.vue'
import DialogMaterialListEdit from '@/components/campAdmin/DialogMaterialListEdit.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import { useMaterialViewHelper } from '@/components/material/useMaterialViewHelper.js'

export default {
  name: 'MaterialDetail',
  components: {
    ContentCard,
    DialogMaterialListEdit,
    MaterialTable,
    PeriodMaterialLists,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Object, required: true },
    materialList: { type: Object, required: true },
  },
  setup(props) {
    return useMaterialViewHelper(props.camp, true)
  },
  head() {
    return {
      title: () => this.materialList.name,
    }
  },
}
</script>
