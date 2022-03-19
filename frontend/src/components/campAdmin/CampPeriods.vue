<!--
Displays all periods of a single camp and allows to edit them & create new ones
-->

<template>
  <content-group>
    <slot name="title">
      <div class="ec-content-group__title py-1 subtitle-1">
        {{ $tc('components.camp.campPeriods.title', api.get().camps().items.length) }}
        <dialog-period-create v-if="!disabled" :camp="camp()">
          <template #activator="{ on }">
            <button-add color="secondary" text
                        class="my-n2"
                        :hide-label="true"
                        v-on="on">
              {{ $tc('components.camp.campPeriods.createPeriod') }}
            </button-add>
          </template>
        </dialog-period-create>
      </div>
    </slot>
    <v-skeleton-loader v-if="camp().periods()._meta.loading" type="article" />
    <v-list>
      <period-item
        v-for="period in periods.items"
        :key="period._meta.self"
        class="px-0"
        :period="period"
        :disabled="disabled" />
    </v-list>
  </content-group>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import PeriodItem from '@/components/campAdmin/CampPeriodsListItem.vue'
import DialogPeriodCreate from './DialogPeriodCreate.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'

export default {
  name: 'CampPeriods',
  components: { ContentGroup, ButtonAdd, PeriodItem, DialogPeriodCreate },
  props: {
    camp: { type: Function, required: true },
    disabled: { type: Boolean, default: false }
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
