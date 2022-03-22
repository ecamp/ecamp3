<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="page_break">
        <h1>Story line for period {{ period.description }}</h1>
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    camp: { type: Object, required: true },
    period: {
      type: Object,
      required: true,
    },
  },
}
</script>

<style lang="scss" scoped>
@media print {
  .page_break {
    page-break-after: always;
  }
}
</style>
