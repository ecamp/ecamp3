<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="TOC">
        <h1>Table of content</h1>
        <p v-for="activity in activities" :key="'toc_' + activity.id">
          <a class="link" :href="'#activity_' + activity.id">{{
            activity.title
          }}</a>
        </p>
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    activities: { type: Array, required: true },
  },
}
</script>

<style lang="scss" scoped>
@media print {
  .TOC {
    page-break-after: always;
  }

  .link::after {
    content: ', page ' target-counter(attr(href url), page);
  }
}
</style>
