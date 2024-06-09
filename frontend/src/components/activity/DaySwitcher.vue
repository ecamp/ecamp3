<template>
  <v-list-item v-if="loading" class="pl-4 pr-0" inactive>
    <v-list-item-title class="flex-grow-0 basis-auto flex-shrink-0">
      <v-skeleton-loader type="text" width="3ch" class="v-skeleton-loader--no-margin" />
    </v-list-item-title>
    <v-list-item-subtitle class="basis-auto flex-shrink-0 ml-2">
      <v-skeleton-loader type="text" class="v-skeleton-loader--no-margin" />
    </v-list-item-subtitle>
    <v-skeleton-loader
      type="avatar"
      width="28"
      height="28"
      class="v-skeleton-loader--no-margin v-skeleton-loader--inherit-size ml-6 mr-1"
    />
    <v-icon>mdi-menu-down</v-icon>
  </v-list-item>
  <e-select
    v-else
    :value="daySelection.number"
    :items="items"
    item-value="number"
    :filled="false"
    return-object
    :menu-props="{
      offsetY: true,
      contentClass: 'e-day-switcher__menu',
      maxHeight: 'min(400px, calc(100vh - 32px))',
    }"
    input-class="e-day-switcher__select mt-0 pt-0"
    @change="changeDay"
  >
    <template #selection="{ index, parent }">
      <v-list-item
        v-if="index === 0"
        class="pl-4 pr-0"
        inactive
        @click.stop="parent.isMenuActive = !parent.isMenuActive"
      >
        <v-list-item-title class="flex-grow-0 basis-num flex-shrink-0 font-weight-bold">
          {{ daySelection.number }}.
        </v-list-item-title>
        <v-list-item-subtitle class="basis-auto flex-shrink-0 ml-2">
          {{ $date.utc(daySelection.start).format('dd. DD. MMM') }}
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
    <template #item="{ item: day, attrs, on }">
      <v-list-item v-bind="attrs" v-on="on">
        <v-list-item-title class="flex-grow-0 basis-num flex-shrink-0 font-weight-bold">
          {{ day.number }}.
        </v-list-item-title>
        <v-list-item-subtitle class="basis-auto flex-shrink-0 ml-2">
          {{ $date.utc(day.start).format('dd. DD. MMM') }}
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
    </template>
  </e-select>
</template>
<script>
import AvatarRow from '@/components/generic/AvatarRow.vue'
import { reduce, sortBy } from 'lodash'

export default {
  name: 'DaySwitcher',
  components: { AvatarRow },
  props: {
    camp: { type: Object, required: true },
    daySelection: { type: Object, required: true },
    loading: { type: Boolean },
  },
  computed: {
    items() {
      return reduce(
        this.periods,
        (result, period, index) => {
          if (index > 0) {
            result.push({ divider: true })
          }
          if (this.periods.length > 1) {
            result.push({ header: period.description })
          }
          result.push(...sortBy(period.days().items, 'number'))
          return result
        },
        []
      )
    },
    periods() {
      return sortBy(this.camp.periods().items, 'start')
    },
  },
  methods: {
    changeDay(value) {
      this.$emit('change-day', value)
    },
  },
}
</script>

<!-- these styles should apply to inner elements -->
<!-- eslint-disable-next-line vue-scoped-css/enforce-style-type -->
<style>
.basis-num {
  flex-basis: 2.5ch;
}

/* .e-day-switcher__menu is in the <e-select> tag */
/*noinspection CssUnusedSymbol*/
.e-day-switcher__menu {
  transform: translateX(-12px);
}

/* ..v-input__append-inner is in an inner tag */
/*noinspection CssUnusedSymbol*/
.e-day-switcher__select .v-input__append-inner {
  align-self: center;
  margin-top: 0;
}
</style>
