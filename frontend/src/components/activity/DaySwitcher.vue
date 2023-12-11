<template>
  <v-list-item v-if="loading" class="px-3">
    <v-list-item-title class="flex-grow-0 basis-auto flex-shrink-0">
      <v-avatar color="rgba(0,0,0,0.1)" size="28">
        <v-skeleton-loader type="text" width="12" class="v-skeleton-loader--no-margin" />
      </v-avatar>
    </v-list-item-title>
    <v-list-item-subtitle class="basis-auto flex-shrink-0 ml-2">
      <v-skeleton-loader type="text" class="v-skeleton-loader--no-margin" />
    </v-list-item-subtitle>
    <v-skeleton-loader
      type="avatar"
      width="28"
      height="28"
      class="v-skeleton-loader--no-margin v-skeleton-loader--inherit-size ml-6"
    />
  </v-list-item>
  <v-menu v-else offset-y>
    <template #activator="{ on }">
      <v-list-item class="px-3" v-on="on">
        <v-list-item-title class="flex-grow-0 basis-auto flex-shrink-0">
          <v-avatar color="rgba(0,0,0,0.07)" size="28">
            {{ daySelection.number }}
          </v-avatar>
        </v-list-item-title>
        <v-list-item-subtitle class="basis-auto flex-shrink-0 ml-2">
          {{ $date.utc(daySelection.start).format('dd. DD.MM.') }}
        </v-list-item-subtitle>
        <AvatarRow
          :camp-collaborations="
            daySelection
              .dayResponsibles()
              .items.map((responsible) => responsible.campCollaboration())
          "
          min-size="28"
          max-size="28"
        />
      </v-list-item>
    </template>
    <v-sheet
      v-for="({ days, period }, key, index) in mappedPeriodDays"
      :key="key"
      class="rounded-t"
    >
      <v-divider v-if="index > 0" />
      <v-list>
        <v-subheader v-if="periods.length > 1">{{ period.description }}</v-subheader>
        <v-list-item
          v-for="day in days"
          :key="day._meta.self"
          @click="$emit('changeDay', day)"
        >
          <v-list-item-title class="flex-grow-0 basis-auto flex-shrink-0">
            <v-avatar
              :color="
                daySelection.id === day.id ? 'rgba(33,150,243,0.17)' : 'rgba(0,0,0,0.07)'
              "
              :class="
                daySelection.id === day.id && 'font-weight-bold blue--text text--darken-4'
              "
              size="28"
            >
              {{ day.number }}
            </v-avatar>
          </v-list-item-title>
          <v-list-item-subtitle class="basis-auto flex-shrink-0 ml-2" style="width: 72px">
            {{ $date.utc(day.start).format('dd. DD.MM.') }}
          </v-list-item-subtitle>
          <AvatarRow
            :camp-collaborations="
              day
                .dayResponsibles()
                .items.map((responsible) => responsible.campCollaboration())
            "
            min-size="28"
            max-size="28"
          />
        </v-list-item>
      </v-list>
    </v-sheet>
  </v-menu>
</template>
<script>
import AvatarRow from '@/components/generic/AvatarRow.vue'
import { keyBy, mapValues, sortBy } from 'lodash'

export default {
  name: 'DaySwitcher',
  components: { AvatarRow },
  props: {
    camp: { type: Function, required: true },
    daySelection: { type: Object, required: true },
    loading: { type: Boolean },
  },
  computed: {
    mappedPeriodDays() {
      return mapValues(keyBy(this.periods, '_meta.self'), (period) => {
        return { period, days: sortBy(period.days().items, 'number') }
      })
    },
    periods() {
      return this.camp().periods().items
    },
  },
}
</script>
