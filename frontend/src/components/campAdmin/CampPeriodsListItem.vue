<!--
Displays a single period as a list item including controls to edit and delete it.
-->

<template>
  <v-list-item v-if="!period._meta.loading">
    <v-list-item-content class="pt-0 pb-2">
      <v-list-item-title>{{ period.description }}</v-list-item-title>
      <v-list-item-subtitle>
        {{ dateRange(period.start, period.end) }}
      </v-list-item-subtitle>
    </v-list-item-content>

    <v-list-item-action v-if="!disabled" style="display: inline">
      <v-item-group>
        <dialog-period-edit :period="period">
          <template #activator="{ on }">
            <button-edit class="mr-1" v-on="on" />
          </template>
        </dialog-period-edit>
      </v-item-group>
    </v-list-item-action>

    <v-menu v-if="!disabled" offset-y>
      <template #activator="{ on, attrs }">
        <v-btn icon v-bind="attrs" v-on="on">
          <v-icon>mdi-dots-vertical</v-icon>
        </v-btn>
      </template>
      <v-card>
        <v-item-group>
          <v-list-item-action>
            <dialog-entity-delete :entity="period" :submit-enabled="!isLastPeriod">
              <template #activator="{ on }">
                <button-delete v-on="on" />
              </template>
              <div v-if="isLastPeriod">
                {{ $tc('components.camp.campPeriodsListItem.lastPeriodNotDeletable') }}
              </div>
              <div v-else>
                {{ $tc('components.camp.campPeriodsListItem.deleteWarning') }} <br />
                <ul>
                  <li>
                    {{ period.description }}
                  </li>
                </ul>
              </div>
            </dialog-entity-delete>
          </v-list-item-action>
        </v-item-group>
      </v-card>
    </v-menu>
  </v-list-item>
</template>

<script>
import DialogPeriodEdit from './DialogPeriodEdit.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'
import { dateRange } from '@/common/helpers/dateHelperUTCFormatted.js'

export default {
  name: 'CampPeriods',
  components: { DialogEntityDelete, DialogPeriodEdit, ButtonEdit, ButtonDelete },
  props: {
    period: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
  },
  computed: {
    isLastPeriod() {
      return this.period.camp().periods().items.length === 1
    },
  },
  methods: {
    dateRange,
  },
}
</script>

<style scoped></style>
