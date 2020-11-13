<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="event">
        <h2 :id="'activity_' + activity.id">
          {{ activity.id }} / {{ activity.title }}
        </h2>
        Category: {{ activity.activityCategory().name }}
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    activity: { type: Object, required: true },
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
