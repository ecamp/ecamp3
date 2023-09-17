<template>
  <v-container fluid>
    <content-card :title="materialList().name" toolbar>
      <template #title-actions>
        <v-menu offset-y>
          <template #activator="{ attrs, on }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list>
            <DialogMaterialListEdit v-if="!isGuest" :material-list="materialList()">
              <template #activator="{ attrs, on }">
                <v-list-item v-bind="attrs" v-on="on">
                  <v-list-item-icon>
                    <v-icon>mdi-pencil</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>{{
                    $tc('global.button.rename')
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
          :material-list="materialList()"
          :disabled="!isContributor"
        />
      </v-expansion-panels>
      <v-card-text v-else-if="collection.length === 1">
        <MaterialTable
          :camp="camp()"
          :material-item-collection="collection[0].materialItems"
          :period="collection[0].period"
          :material-list="materialList()"
          :disabled="!isContributor"
        />
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import PeriodMaterialLists from '@/components/material/PeriodMaterialLists.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import DialogMaterialListEdit from '@/components/campAdmin/DialogMaterialListEdit.vue'
import MaterialTable from '@/components/material/MaterialTable.vue'
import slugify from 'slugify'
import { useDownloadMaterialList } from '@/components/material/useDownloadMaterialList.js'

export default {
  name: 'MaterialDetail',
  components: {
    MaterialTable,
    DialogMaterialListEdit,
    PeriodMaterialLists,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Function, required: true },
    materialList: { type: Function, required: true },
  },
  setup({ camp }) {
    const { downloadMaterialList } = useDownloadMaterialList(camp, false)
    return {
      downloadMaterialList,
    }
  },
  data() {
    return {
      openPeriods: [],
    }
  },
  computed: {
    collection() {
      return this.camp()
        .periods()
        .items.map((period) => ({
          period,
          materialItems: this.api.get().materialItems({
            materialList: this.materialList()._meta.self,
            period: period._meta.self,
          }),
        }))
    },
  },
  mounted() {
    this.camp()
      .periods()
      ._meta.load.then((periods) => {
        this.openPeriods = periods.items.reduce((result, period, index) => {
          if (Date.parse(period.end) >= new Date()) {
            result.push(index)
          }
          return result
        }, [])
      })
  },
  methods: {
    async downloadXlsx() {
      await this.downloadMaterialList(
        this.collection,
        [
          slugify(this.$tc('entity.materialList.name')),
          slugify(this.materialList().name),
          this.$date().format('YYMMDDHHmmss'),
        ].join('_')
      )
    },
  },
}
</script>
