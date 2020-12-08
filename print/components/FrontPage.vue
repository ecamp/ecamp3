<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="page_break">
        <p>{{ $tc('welcome.print') }}</p>

        <p>{{ $tc('welcome.common') }}</p>

        <h1>Title page</h1>

        <p>Name: {{ camp.name }}</p>
        <p>Title: {{ camp.title }}</p>
        <p>Motto: {{ camp.motto }}</p>

        <div v-for="period in periods" :key="'period_' + period.id">
          {{ period.id }} // {{ period.description }}
        </div>
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      periods: null,
    }
  },
  async fetch() {
    this.periods = (await this.camp.periods()._meta.load).items
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
