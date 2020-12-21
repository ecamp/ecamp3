<!--
Displays all periods of a single camp and allows to edit them & create new ones
-->

<template>
  <content-group>
    <slot name="title">
      <div class="ec-content-group__title py-1 subtitle-1">
        {{ $tc('components.camp.campPeriods.title', api.get().camps().items.length) }}
        <dialog-period-create :camp="camp()">
          <template v-slot:activator="{ on }">
            <button-add color="secondary" text
                        :hide-label="true"
                        v-on="on">
              {{ $tc('components.camp.campPeriods.createPeriod') }}
            </button-add>
          </template>
        </dialog-period-create>
      </div>
    </slot>
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <v-list>
      <period-item
        v-for="period in periods.items"
        :key="period.id"
        class="px-0"
        :period="period" />
    </v-list>
  </content-group>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd'
import PeriodItem from '@/components/camp/CampPeriodsListItem'
import DialogPeriodCreate from '@/components/dialog/DialogPeriodCreate'
import ContentGroup from '@/components/layout/ContentGroup'

export default {
  name: 'CampPeriods',
  components: { ContentGroup, ButtonAdd, PeriodItem, DialogPeriodCreate },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {}
  },
  computed: {
    periods () {
      return this.camp().periods()
    }
  }
}
</script>

<style scoped>
</style>
