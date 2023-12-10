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
          <v-avatar color="rgba(0,0,0,0.1)" size="28">
            {{ currentDayAsObject.value.number }}
          </v-avatar>
        </v-list-item-title>
        <v-list-item-subtitle class="basis-auto flex-shrink-0 ml-2">
          {{ currentDayAsObject.text }}
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
    <v-list>
      <v-list-item
        v-for="day in sortedDays"
        :key="day.value._meta.self"
        @click="$emit('changeDay', day.value)"
      >
        <v-list-item-title class="flex-grow-0 basis-auto flex-shrink-0">
          <v-avatar color="rgba(0,0,0,0.1)" size="28">{{ day.value.number }}</v-avatar>
        </v-list-item-title>
        <v-list-item-subtitle class="basis-auto flex-shrink-0 ml-2" style="width: 72px">
          {{ day.text }}
        </v-list-item-subtitle>
        <AvatarRow
          :camp-collaborations="
            day.value
              .dayResponsibles()
              .items.map((responsible) => responsible.campCollaboration())
          "
          min-size="28"
          max-size="28"
        />
      </v-list-item>
    </v-list>
  </v-menu>
</template>
<script>
import AvatarRow from '@/components/generic/AvatarRow.vue'

export default {
  name: 'DaySwitcher',
  components: { AvatarRow },
  props: {
    period: { type: Function, required: true },
    daySelection: { type: Object, required: true },
    loading: { type: Boolean },
  },
  computed: {
    days() {
      return this.period()
        .days()
        .items.map((day) => this.getDayObject(day))
    },
    sortedDays() {
      const days = [...this.days]
      days.sort((a, b) => new Date(a.value.start) - new Date(b.value.start))
      return days
    },
    currentDayAsObject() {
      return this.getDayObject(this.daySelection)
    },
  },
  methods: {
    getDayObject(day) {
      return {
        text: this.$date.utc(day.start).format('dd. DD.MM.'),
        value: day,
      }
    },
  },
}
</script>
