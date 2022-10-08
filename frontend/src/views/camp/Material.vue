<!--
Admin screen of a camp: Displays MaterialLists and MaterialItems
-->

<template>
  <content-card :title="$tc('views.camp.material.title')" toolbar>
    <template #title-actions>
      <e-switch
        v-model="showActivityMaterial"
        class="ml-15"
        :label="
          $vuetify.breakpoint.smAndUp
            ? $tc('views.camp.material.showActivityMaterial')
            : $tc('views.camp.material.showActivityMaterialShort')
        "
      />

      <e-switch
        v-if="$vuetify.breakpoint.smAndUp"
        v-model="groupByList"
        class="ml-10"
        :label="$tc('views.camp.material.groupByList')"
      />

      <v-btn small color="primary" class="ml-5" @click="downloadXlsx">
        <v-icon left>mdi-microsoft-excel</v-icon>
        {{ $tc('views.camp.material.downloadXlsx') }}
      </v-btn>
    </template>
    <v-expansion-panels v-model="openPeriods" multiple flat accordion>
      <period-material-lists
        v-for="period in camp().periods().items"
        :key="period._meta.self"
        :period="period"
        :show-activity-material="showActivityMaterial"
        :group-by-list="groupByList || $vuetify.breakpoint.xs"
        :disabled="!isContributor"
      />
    </v-expansion-panels>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import PeriodMaterialLists from '@/components/material/PeriodMaterialLists.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import * as XLSX from 'xlsx'

export default {
  name: 'Material',
  components: {
    ContentCard,
    PeriodMaterialLists,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Function, required: true },
  },
  data() {
    return {
      openPeriods: [],
      showActivityMaterial: false,
      groupByList: false,
    }
  },
  watch: {
    showActivityMaterial(val) {
      localStorage.viewCampMaterialShowActivityMaterial = val ? 'true' : 'false'
    },
  },
  mounted() {
    if (localStorage.viewCampMaterialShowActivityMaterial === undefined) {
      localStorage.viewCampMaterialShowActivityMaterial = 'false'
    }
    this.showActivityMaterial =
      localStorage.viewCampMaterialShowActivityMaterial === 'true'

    this.camp()
      .periods()
      ._meta.load.then((periods) => {
        this.openPeriods = periods.items
          .map((period, idx) => (Date.parse(period.end) >= new Date() ? idx : null))
          .filter((idx) => idx !== null)
      })

    this.camp().activities().$reload()
  },
  methods: {
    async downloadXlsx() {
      await this.camp().activities().$loadItems()

      let workbook = XLSX.utils.book_new()

      let sheets = await Promise.all(
        this.camp()
          .periods()
          .items.map(async (p) => {
            let rows = await Promise.all(
              p.materialItems().items.map(async (mi) => {
                let activity = await this.getActivity(mi)
                return [
                  mi.quantity,
                  mi.unit,
                  mi.article,
                  mi.materialList().name,
                  activity?.title,
                ]
              })
            )

            let data = [
              [
                this.$tc('views.camp.material.xlsx.quantity'),
                this.$tc('views.camp.material.xlsx.unit'),
                this.$tc('views.camp.material.xlsx.article'),
                this.$tc('views.camp.material.xlsx.matlist'),
                this.$tc('views.camp.material.xlsx.activity'),
              ],
            ]
            rows.forEach((r) => data.push(r))

            return { description: p.description, data }
          })
      )

      sheets.forEach((s) => {
        let worksheet = XLSX.utils.aoa_to_sheet(s.data)
        let validSheetName = s.description.replaceAll(/[?*[\]/\\:]/g, '')
        workbook.SheetNames.push(validSheetName)
        workbook.Sheets[validSheetName] = worksheet
      })

      XLSX.writeFile(workbook, this.$tc('views.camp.material.xlsx.filename') + '.xlsx')
    },

    async getActivity(mi) {
      if (mi.materialNode) {
        const root = await mi.materialNode().$href('root')
        return this.camp()
          .activities()
          .items.find((activity) => {
            return activity.rootContentNode()._meta.self === root
          })
      }
      return null
    },
  },
}
</script>

<style scoped></style>
