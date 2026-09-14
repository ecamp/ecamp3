<!--
Displays a single period as a list item including controls to edit and delete it.
-->

<template>
  <v-list-item v-if="!period._meta.loading">
    <v-list-item-title>{{ period.description }}</v-list-item-title>
    <v-list-item-subtitle>
      {{ dateRange(period.start, period.end) }}
    </v-list-item-subtitle>

    <template #append>
      <v-menu v-if="!disabled" v-model="showMenuEdit" offset-y>
        <template #activator="{ props }">
          <v-btn variant="text" icon v-bind="props">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list>
          <dialog-period-description-edit :period="period" @closed="showMenuEdit = false">
            <template #activator="{ props }">
              <v-list-item v-bind="props">
                <template #prepend>
                  <v-icon>mdi-pencil</v-icon>
                </template>
                <v-list-item-title>{{
                  $t('components.campAdmin.campPeriodsListItem.changePeriodDescription')
                }}</v-list-item-title>
              </v-list-item>
            </template>
          </dialog-period-description-edit>

          <dialog-period-date-edit
            :period="period"
            mode="move"
            @closed="showMenuEdit = false"
          >
            <template #activator="{ props }">
              <v-list-item v-bind="props">
                <template #prepend>
                  <v-icon>mdi-arrow-left-right</v-icon>
                </template>
                <v-list-item-title>
                  {{ $t('components.campAdmin.campPeriodsListItem.movePeriod') }}
                </v-list-item-title>
              </v-list-item>
            </template>
          </dialog-period-date-edit>

          <dialog-period-date-edit
            :period="period"
            mode="changeStart"
            @closed="showMenuEdit = false"
          >
            <template #activator="{ props }">
              <v-list-item v-bind="props">
                <template #prepend>
                  <v-icon>mdi-arrow-collapse-left</v-icon>
                </template>
                <v-list-item-title>
                  {{
                    $t('components.campAdmin.campPeriodsListItem.periodChangeStart')
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
            <template #activator="{ props }">
              <v-list-item v-bind="props">
                <template #prepend>
                  <v-icon>mdi-arrow-collapse-right</v-icon>
                </template>
                <v-list-item-title>
                  {{
                    $t('components.campAdmin.campPeriodsListItem.periodChangeEnd')
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
            <template #activator="{ props }">
              <v-list-item v-bind="props">
                <template #prepend>
                  <v-icon>mdi-delete</v-icon>
                </template>
                <v-list-item-title>
                  {{ $t('global.button.delete') }}
                </v-list-item-title>
              </v-list-item>
            </template>
            <div v-if="isLastPeriod">
              {{ $t('components.campAdmin.campPeriodsListItem.lastPeriodNotDeletable') }}
            </div>
            <div v-else>
              {{ $t('components.campAdmin.campPeriodsListItem.deleteWarning') }} <br />
              <ul>
                <li>
                  {{ period.description }}
                </li>
              </ul>
            </div>
          </dialog-entity-delete>
        </v-list>
      </v-menu>
    </template>
  </v-list-item>
</template>

<script>
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import DialogPeriodDateEdit from './DialogPeriodDateEdit.vue'
import DialogPeriodDescriptionEdit from './DialogPeriodDescriptionEdit.vue'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'

export default {
  name: 'CampPeriods',
  components: { DialogEntityDelete, DialogPeriodDescriptionEdit, DialogPeriodDateEdit },
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
