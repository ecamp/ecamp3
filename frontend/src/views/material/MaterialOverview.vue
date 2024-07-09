<template>
  <v-container fluid>
    <content-card :title="$tc('views.material.materialOverview.title')" toolbar>
      <template #title-actions>
        <v-menu offset-y>
          <template #activator="{ attrs, on }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list class="py-0">
            <DialogMaterialListCreate v-if="!isGuest" :camp="camp">
              <template #activator="{ attrs, on }">
                <v-list-item v-bind="attrs" v-on="on">
                  <v-list-item-icon>
                    <v-icon>mdi-plus</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content
                    >{{ $tc('views.material.materialOverview.createNewList') }}
                  </v-list-item-content>
                </v-list-item>
              </template>
            </DialogMaterialListCreate>
            <v-list-item @click="downloadXlsx">
              <v-list-item-icon>
                <v-icon>mdi-microsoft-excel</v-icon>
              </v-list-item-icon>
              <v-list-item-content
                >{{ $tc('views.material.materialOverview.download') }}
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
      <v-expansion-panels
        v-if="collection.length > 1"
        v-model="openPeriods"
        multiple
        flat
        accordion
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
}
</script>
