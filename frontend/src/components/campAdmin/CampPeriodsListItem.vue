<!--
Displays a single period as a list item including controls to edit and delete it.
-->

<template>
  <dialog-period-edit :period="period">
    <template #activator="{ on }">
      <v-hover v-slot="{ hover }">
        <v-list-item v-if="!period._meta.loading" class="px-sm-0" v-on="on">
          <v-list-item-content>
            <v-list-item-title>{{ period.description }}</v-list-item-title>
            <v-list-item-subtitle>
              {{ $date.utc(period.start).format($tc('global.datetime.dateLong')) }} -
              {{ $date.utc(period.end).format($tc('global.datetime.dateLong')) }}
            </v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-icon class="align-self-center">
            <v-icon v-show="hover" small>mdi-pencil</v-icon>
          </v-list-item-icon>
        </v-list-item>
      </v-hover>
    </template>
  </dialog-period-edit>
</template>

<script>

import DialogPeriodEdit from './DialogPeriodEdit.vue'

export default {
  name: 'CampPeriods',
  components: {
    DialogPeriodEdit
  },
  props: {
    period: {
      type: Object,
      required: true
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    isLastPeriod () {
      return this.period.camp().periods().items.length === 1
    }
  }
}
</script>

<style scoped>
</style>
