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
            <DialogMaterialListCreate v-if="!isGuest" :camp="camp()">
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
          :camp="camp()"
          :material-item-collection="collection[0].materialItems"
          :period="collection[0].period"
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
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import DialogMaterialListCreate from '@/components/campAdmin/DialogMaterialListCreate.vue'
import slugify from 'slugify'
import { useDownloadMaterialList } from '@/components/material/useDownloadMaterialList.js'

export default {
  name: 'MaterialOverview',
  components: {
    DialogMaterialListCreate,
    PeriodMaterialLists,
    MaterialTable,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Function, required: true },
  },
  setup({ camp }) {
    const { downloadMaterialList } = useDownloadMaterialList(camp, true)
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
            period: period._meta.self,
          }),
        }))
    },
  },
  mounted() {
    this.camp().activities().$reload()
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
          slugify(this.camp().name),
          slugify(this.$tc('views.material.materialOverview.title')),
          this.$date().format('YYMMDDHHmmss'),
        ].join('_')
      )
    },
  },
}
</script>
