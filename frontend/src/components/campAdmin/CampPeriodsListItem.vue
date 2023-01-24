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

    <v-menu v-if="!disabled" v-model="showMenuEdit" offset-y>
      <template #activator="{ on, attrs }">
        <v-btn icon v-bind="attrs" v-on="on">
          <v-icon>mdi-dots-vertical</v-icon>
        </v-btn>
      </template>
      <v-list>
        <dialog-period-desc-edit :period="period" @closed="showMenuEdit = false">
          <template #activator="{ on, attrs }">
            <v-list-item v-bind="attrs" v-on="on">
              <v-list-item-icon>
                <v-icon>mdi-pencil</v-icon>
              </v-list-item-icon>
              <v-list-item-title>{{
                $tc('components.campAdmin.campPeriodsListItem.changePeriodDescription')
              }}</v-list-item-title>
            </v-list-item>
          </template>
        </dialog-period-desc-edit>

        <dialog-period-date-edit
          :period="period"
          mode="move"
          @closed="showMenuEdit = false"
        >
          <template #activator="{ on, attrs }">
            <v-list-item v-bind="attrs" v-on="on">
              <v-list-item-icon>
                <v-icon>mdi-arrow-left-right</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{ $tc('components.campAdmin.campPeriodsListItem.movePeriod') }}
              </v-list-item-title>
            </v-list-item>
          </template>
        </dialog-period-date-edit>

        <dialog-period-date-edit
          :period="period"
          mode="changeStart"
          @closed="showMenuEdit = false"
        >
          <template #activator="{ on, attrs }">
            <v-list-item v-bind="attrs" v-on="on">
              <v-list-item-icon>
                <v-icon>mdi-arrow-collapse-left</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{
                  $tc('components.campAdmin.campPeriodsListItem.periodChangeStart')
                }}</v-list-item-title
              >
            </v-list-item>
          </template>
        </dialog-period-date-edit>

        <dialog-period-date-edit
          :period="period"
          mode="changeEnd"
          @closed="showMenuEdit = false"
        >
          <template #activator="{ on, attrs }">
            <v-list-item v-bind="attrs" v-on="on">
              <v-list-item-icon>
                <v-icon>mdi-arrow-collapse-right</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{
                  $tc('components.campAdmin.campPeriodsListItem.periodChangeEnd')
                }}</v-list-item-title
              >
            </v-list-item>
          </template>
        </dialog-period-date-edit>

        <v-divider />

        <dialog-entity-delete
          :entity="period"
          :submit-enabled="!isLastPeriod"
          @closed="showMenuEdit = false"
        >
          <template #activator="{ on }">
            <v-list-item v-on="on">
              <v-list-item-icon>
                <v-icon>mdi-delete</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{ $tc('global.button.delete') }}
              </v-list-item-title>
            </v-list-item>
          </template>
          <div v-if="isLastPeriod">
            {{ $tc('components.campAdmin.campPeriodsListItem.lastPeriodNotDeletable') }}
          </div>
          <div v-else>
            {{ $tc('components.campAdmin.campPeriodsListItem.deleteWarning') }} <br />
            <ul>
              <li>
                {{ period.description }}
              </li>
            </ul>
          </div>
        </dialog-entity-delete>
      </v-list>
    </v-menu>
  </v-list-item>
</template>

<script>
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import DialogPeriodDateEdit from './DialogPeriodDateEdit.vue'
import DialogPeriodDescEdit from './DialogPeriodDescEdit.vue'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'

export default {
  name: 'CampPeriods',
  components: { DialogEntityDelete, DialogPeriodDescEdit, DialogPeriodDateEdit },
  mixins: [dateHelperUTCFormatted],
  props: {
    period: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
  },
  data() {
    return {
      showMenuEdit: false,
    }
  },
  computed: {
    isLastPeriod() {
      return this.period.camp().periods().items.length === 1
    },
  },
}
</script>

<style scoped></style>
