<template>
  <v-row v-if="!$fetchState.pending" no-gutters>
    <v-col cols="12">
      <div class="event">
        <h2 :id="'activity_' + activity.id">
          {{ activity.id }} / {{ activity.title }}
        </h2>
        Category: {{ activityCategory.name }}
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    scheduleEntry: { type: Object, required: true },
  },
  data() {
    return {
      activity: null,
      activityCategory: null,
    }
  },
  async fetch() {
    this.activity = await this.scheduleEntry.activity()._meta.load
    this.activityCategory = await this.activity.activityCategory()._meta.load
  },
}
</script>

<style lang="scss" scoped>
@media print {
  .event {
    page-break-after: always;
  }
}
</style>
