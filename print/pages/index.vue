<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div v-if="true">
        <front-page v-if="config.showFrontpage" :camp="camp" />
      </div>

      <table-of-content v-if="config.showToc" :activities="activities" />

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
function collection(items) {
  const entityArray = items.map((item) => entity(item)())

  return () => ({
    items: entityArray,
    _meta: {
      load: Promise.resolve({
        items: entityArray,
      }),
    },
  })
}

function entity(item) {
  return () =>
    Object.assign(
      { ...item },
      {
        _meta: {
          load: Promise.resolve({ ...item }),
          self: item.id,
        },
      }
    )
}

export default {
  data() {
    return {
      config: {},
      pagedjs: '',
      camp: null,
      activities: null,
    }
  },
  fetch() {
    /*
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
    */

    this.config = {
      showFrontpage: true,
      showToc: true,
      showPicasso: true,
      showStoryline: true,
      showDailySummary: true,
      showActivities: true,
    }

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
                  id: '/schedule_entry/1',
                  activity: entity({
                    location: 'Lagerplatz',
                    category: entity({
                      short: 'LA',
                      color: '#55AAAA',
                    }),
                    scheduleEntries: collection([
                      {
                        id: '/schedule_entry/1',
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

    this.activities = [
      {
        id: 'activity1',
        title: 'Activity 1',
      },
      {
        id: 'activity2',
        title: 'Activity 2',
      },
    ]
  },
}
</script>
