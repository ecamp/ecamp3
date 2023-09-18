<!--
Displays several tabs with details on a single camp.
-->

<template>
  <v-container fluid>
    <content-card :title="materialList().name" toolbar>
      <template #title-actions>
        <DialogMaterialListEdit :material-list="materialList()">
          <template #activator="{ on }">
            <ButtonEdit class="mr-n2" height="32" text bg v-on="on" />
          </template>
        </DialogMaterialListEdit>
      </template>
      <v-expansion-panels v-model="openPeriods" multiple flat accordion>
        <PeriodMaterialLists
          v-for="period in camp().periods().items"
          :key="period._meta.self"
          :period="period"
          :camp="camp()"
          :material-list="materialList()"
          :disabled="!isContributor"
        />
      </v-expansion-panels>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import PeriodMaterialLists from '@/components/material/PeriodMaterialListsPanel.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import DialogMaterialListEdit from '@/components/material/DialogMaterialListEdit.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'

export default {
  name: 'MaterialDetail',
  components: {
    ButtonEdit,
    DialogMaterialListEdit,
    PeriodMaterialLists,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Function, required: true },
    materialList: { type: Function, required: true },
  },
  data() {
    return {
      locked: false,
      openPeriods: [],
      showActivityMaterial: false,
      groupByList: false,
    }
  },
  computed: {
    collection() {
      return this.api.get().materialItems({
        materialList: this.materialList()._meta.self,
        period: this.period()._meta.self,
      })
    },
  },
  mounted() {
    this.camp()
      .periods()
      ._meta.load.then((periods) => {
        this.openPeriods = periods.items
          .map((period, idx) => (Date.parse(period.end) >= new Date() ? idx : null))
          .filter((idx) => idx !== null)
      })
  },
}
</script>
