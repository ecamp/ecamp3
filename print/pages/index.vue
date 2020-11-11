<template>
  <v-row no-gutters>
    <v-col cols="12">
      <front-page :camp="camp" />

      <toc :activities="activities" />

      <picasso />

      <activity
        v-for="activity in activities"
        :key="'activity_' + activity.id"
        :activity="activity"
      />
    </v-col>
  </v-row>
</template>

<script>
export default {
  async asyncData({ query, $api }) {
    const [campData, activityData] = await Promise.all([
      $api.get().camps({ campId: query.camp })._meta.load,
      $api.get().activities({ campId: query.camp })._meta.load,
    ])

    return {
      query,
      camp: campData,
      activities: activityData.items,
    }
  },
  head() {
    if (this.query.pagedjs === 'true') {
      return {
        script: [
          {
            src: 'pagedConfig.js',
          },
          {
            src: 'https://unpkg.com/pagedjs/dist/paged.polyfill.js',
          },
        ],
        link: [
          {
            rel: 'stylesheet',
            href: 'print-preview.css',
          },
        ],
      }
    }
  },
}
</script>

<style lang="scss" scoped>
@media print {
  @page {
    size: a4 portrait;

    @top-left {
      content: 'eCamp3';
    }

    @top-center {
      content: 'Placeholder Lagertitel';
    }
    @bottom-left {
      content: counter(page) ' of ' counter(pages);
    }
  }
}
</style>
