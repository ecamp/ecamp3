<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="TOC">
        <h1>Table of content</h1>
        <p
          v-for="scheduleEntry in scheduleEntries"
          :key="'toc_' + scheduleEntry.id"
        >
          <a class="toclink" :href="'#scheduleEntry_' + scheduleEntry.id">{{
            scheduleEntry.title
          }}</a>
        </p>
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    scheduleEntries: { type: Array, required: true },
  },
}
</script>

<style lang="scss" scoped>
@media print {
  .TOC {
    page-break-after: always;
  }
}
</style>
