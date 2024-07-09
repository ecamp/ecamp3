<template>
  <v-container fluid>
    <content-card :title="materialList.name" toolbar>
      <template #title-actions>
        <v-menu offset-y>
          <template #activator="{ attrs, on }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list class="py-0">
            <DialogMaterialListEdit v-if="!isGuest" :material-list="materialList">
              <template #activator="{ attrs, on }">
                <v-list-item v-bind="attrs" v-on="on">
                  <v-list-item-icon>
                    <v-icon>mdi-pencil</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>{{
                    $tc('global.button.edit')
                  }}</v-list-item-content>
                </v-list-item>
              </template>
            </DialogMaterialListEdit>
            <v-list-item @click="downloadXlsx">
              <v-list-item-icon>
                <v-icon>mdi-microsoft-excel</v-icon>
              </v-list-item-icon>
              <v-list-item-content>{{
                $tc('global.button.download')
              }}</v-list-item-content>
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
}
</script>
