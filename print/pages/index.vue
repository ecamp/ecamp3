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
  async asyncData({ query, $axios }) {
    const [campData, activityData] = await Promise.allSettled([
      $axios.$get(`/camps/${query.camp}`),
      $axios.$get(`/activities?campId=${query.camp}`),
    ])

    return {
      query,
      camp: campData.value,
      activities: activityData.value._embedded.items,
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
