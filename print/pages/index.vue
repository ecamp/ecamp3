<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div v-for="(content, idx) in config.contents" :key="idx">
        <component
          :is="'Config' + content.type"
          :options="content.options"
          :camp="camp"
        />
      </div>
      <!--
      <hr />
      <h1>API connection test</h1>
      Loading all camps from API to check API connection &amp; authentication
      <ul>
        <li v-for="displayedCamp in camps" :key="displayedCamp.id">
          {{ displayedCamp.id }} / {{ displayedCamp.name }} /
          {{ displayedCamp.title }}
        </li>
      </ul>
      <hr />
      test

      <div v-if="true">
        <front-page v-if="config.showFrontpage" :camp="camp" />
      </div>

      <table-of-content
        v-if="config.showToc"
        :schedule-entries="scheduleEntries"
      />

      <picasso v-if="config.showPicasso" :camp="camp" type="4day" />

      <picasso
        v-if="config.showPicasso"
        :camp="camp"
        :rotate="true"
        type="week"
      />

      <storyline v-if="config.showStoryline" :camp="camp" />

      <program
        v-if="config.showDailySummary || config.showActivities"
        :camp="camp"
        :show-daily-summary="config.showDailySummary"
        :show-activities="config.showActivities"
      /> -->
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
    }
  },
  async fetch() {
    const query = this.$route.query

    this.config = JSON.parse(query.config)

    try {
      this.camp = await this.$api.get(this.config.camp)._meta.load
    } catch (error) {
      // eslint-disable-next-line
      console.log(error)
    }

    /*
    if (query.period) {
      this.period = await this.$api.get(query.period)._meta.load
      this.camp = await this.period.camp()._meta.load

      const [scheduleEntries] = await Promise.all([
        this.period.scheduleEntries().$loadItems(),
        this.camp.activities().$loadItems(),
        this.camp.categories().$loadItems(),
      ])

      this.scheduleEntries = scheduleEntries.items
    } else {
      this.camp = {
        name: 'Camp Name',
        title: 'camp title',
        motto: 'camp motto',
        periods: collection([
          {
            id: '/camp/1',
            description: 'Vorlager',
            days: collection([
              {
                id: '/day/1',
                dayOffset: 1,
                scheduleEntries: collection([
                  {
                    id: 'scheduleEntry1',
                    activity: entity({
                      title: 'Activity 1',
                      location: 'Lagerplatz',
                      category: entity({
                        short: 'LA',
                        color: '#55AAAA',
                      }),
                      scheduleEntries: collection([
                        {
                          id: 'scheduleEntry1',
                          number: '1.1',
                          periodOffset: 510,
                          length: 45,
                          period: entity({
                            start: '2021-11-25',
                          }),
                        },
                      ]),
                    }),
                  },
                ]),
              },
            ]),
          },
        ]),
      }

      this.scheduleEntries = [
        {
          id: 'scheduleEntry1',
          activity: entity({
            title: 'Activity 1',
          }),
        },
      ]
    }
    */
  },
}
</script>
