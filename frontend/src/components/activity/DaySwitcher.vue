<template>
  <v-list-item v-if="loading" class="gap-2">
    <v-list-item-title class="flex-grow-0 basis-auto flex-shrink-0">
      <v-avatar color="grey lighten-2" size="28">
        <v-skeleton-loader type="text" width="12" class="v-skeleton-loader--no-margin" />
      </v-avatar>
    </v-list-item-title>
    <v-list-item-subtitle class="basis-auto flex-shrink-0">
      <v-skeleton-loader type="text" class="v-skeleton-loader--no-margin" />
    </v-list-item-subtitle>
  </v-list-item>
  <v-menu v-else offset-y>
    <template #activator="{ on }">
      <v-list-item v-on="on">
        <v-list-item-title class="flex-grow-0 basis-auto flex-shrink-0">
          <v-avatar color="grey lighten-2" size="28">{{
            currentDayAsObject.value.number
          }}</v-avatar>
        </v-list-item-title>
        <v-list-item-subtitle class="basis-auto flex-shrink-0 ml-2">
          {{ currentDayAsObject.text }}
        </v-list-item-subtitle>
        <v-list-item-subtitle class="basis-auto flex-grow-0 ml-2">
          <DayResponsibles :date="currentDayAsString" :period="period()" readonly />
        </v-list-item-subtitle>
      </v-list-item>
    </template>
    <v-list>
      <v-list-item
        v-for="day in days"
        :key="day.value._meta.self"
        @click="$emit('changeDay', day.value)"
      >
        <v-list-item-title class="flex-grow-0 basis-auto flex-shrink-0">
          <v-avatar color="grey lighten-2" size="28">{{ day.value.number }}</v-avatar>
        </v-list-item-title>
        <v-list-item-subtitle
          class="basis-auto flex-shrink-0 flex-grow-0 ml-2"
          style="width: 72px"
        >
          {{ day.text }}
        </v-list-item-subtitle>
        <v-list-item-subtitle class="basis-auto select-left ml-2">
          <DayResponsibles :date="day.iso" :period="period()" readonly />
        </v-list-item-subtitle>
      </v-list-item>
    </v-list>
  </v-menu>
</template>
<script>
import DayResponsibles from '@/components/program/picasso/DayResponsibles.vue'

export default {
  name: 'DaySwitcher',
  components: { DayResponsibles },
  props: {
    period: { type: Function, required: true },
    daySelection: { type: Object, required: true },
    currentDayAsString: { type: String, required: true },
    loading: { type: Boolean },
  },
  computed: {
    days() {
      return this.period()
        .days()
        .items.map((day) => this.getDayObject(day))
    },
    currentDayAsObject() {
      return this.getDayObject(this.daySelection)
    },
  },
  methods: {
    getDayObject(day) {
      return {
        text: this.$date.utc(day.start).format('dd. DD.MM.'),
        iso: day.start,
        value: day,
      }
    },
  },
}
</script>
