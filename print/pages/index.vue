<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div v-if="true">
        <front-page v-if="config.showFrontpage" :camp="camp" />
      </div>

      <toc v-if="config.showToc" :activities="activities" />

      <picasso v-if="config.showPicasso" :camp="camp" />

      <storyline v-if="config.showStoryline" :camp="camp" />

      <program
        v-if="config.showDailySummary || config.showActivities"
        :camp="camp"
        :show-daily-summary="config.showDailySummary"
        :show-activities="config.showActivities"
      />

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
  async fetch() {
    const query = this.$nuxt.context.query

    this.pagedjs = query.pagedjs

    this.config = {
      showFrontpage: query.showFrontpage === 'true',
      showToc: query.showToc === 'true',
      showPicasso: query.showPicasso === 'true',
      showStoryline: query.showStoryline === 'true',
      showDailySummary: query.showDailySummary === 'true',
      showActivities: query.showActivities === 'true',
    }

    this.camp = await this.$api.get().camps({ campId: query.camp })._meta.load
    this.activities = (await this.camp.activities()._meta.load).items
  },
  data() {
    return {
      config: {},
      pagedjs: '',
      camp: null,
      activities: null,
    }
  },
  head() {
    if (this.pagedjs === 'true') {
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
