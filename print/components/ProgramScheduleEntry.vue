<template>
  <v-row no-gutters>
    <v-col cols="12">
      <error v-if="$fetchState.error">{{ $fetchState.error.message }}</error>
      <div v-else-if="!$fetchState.pending" class="event">
        <h2 :id="'activity_' + activity.id">
          {{ activity.id }} {{ scheduleEntry.id }}/ {{ activity.title }}
        </h2>
        Category: {{ category.name }}
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
      category: null,
    }
  },
  async fetch() {
    this.activity = await this.scheduleEntry.activity()._meta.load
    this.category = await this.activity.category()._meta.load
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
