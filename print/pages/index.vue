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
    </v-col>
  </v-row>
</template>

<script>
export default {
  data() {
    return {
      config: {},
      pagedjs: '',
      camp: null,
      activities: null,
    }
  },
  async fetch() {
    const query = this.$route.query

    this.config = {
      showFrontpage:
        query.showFrontpage && query.showFrontpage.toLowerCase() === 'true',
      showToc: query.showToc && query.showToc.toLowerCase() === 'true',
      showPicasso:
        query.showPicasso && query.showPicasso.toLowerCase() === 'true',
      showStoryline:
        query.showStoryline && query.showStoryline.toLowerCase() === 'true',
      showDailySummary:
        query.showDailySummary &&
        query.showDailySummary.toLowerCase() === 'true',
      showActivities:
        query.showActivities && query.showActivities.toLowerCase() === 'true',
    }

    this.camp = await this.$api.get().camps({ campId: query.camp })._meta.load
    this.activities = (await this.camp.activities()._meta.load).items
  },
}
</script>
