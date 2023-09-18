<!--
Displays several tabs with details on a single camp.
-->

<template>
  <v-container fluid>
    <content-card :title="$tc('views.material.materialOverview.title')" toolbar>
      <template #title-actions>
        <v-btn small color="primary" height="32" class="mr-n2" @click="downloadXlsx">
          <v-icon left>mdi-microsoft-excel</v-icon>
          {{ $tc('views.material.materialOverview.downloadXlsx') }}
        </v-btn>
      </template>
      <div :class="{ 'px-4 pt-4': $vuetify.breakpoint.smAndUp }">
        <MaterialTable
          :camp="camp()"
          :material-item-collection="collection"
          show-activity-material
        />
      </div>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import MaterialTable from '@/components/material/MaterialTable.vue'
import * as XLSX from 'xlsx'

export default {
  name: 'MaterialOverview',
  components: { MaterialTable, ContentCard },
  props: {
    camp: { type: Function, required: true },
  },
  computed: {
    collection() {
      return this.api.get().materialItems({
        camp: this.camp()._meta.self,
      })
    },
  },
  mounted() {
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
                  activity?.title || mi.period().description,
                ]
              })
            )

            let data = [
              [
                this.$tc('entity.materialItem.fields.quantity'),
                this.$tc('entity.materialItem.fields.unit'),
                this.$tc('entity.materialItem.fields.article'),
                this.$tc('entity.materialItem.fields.list'),
                this.$tc('entity.materialItem.fields.reference'),
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

      XLSX.writeFile(
        workbook,
        this.$tc('views.material.materialOverview.xlsx.filename') + '.xlsx'
      )
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
