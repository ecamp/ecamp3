<template>
  <v-row no-gutters>
    <v-col cols="12">
      <picasso-landscape :camp="camp" />
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
  layout: 'landscapeA3',
  data() {
    return {
      config: {},
      pagedjs: '',
      camp: null,
      camps: [],
      activities: null,
    }
  },
  async fetch() {
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

    try {
      this.camps = (await this.$api.get().camps()._meta.load).items
    } catch (error) {
      console.log(error)
    }

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
        title: 'Activity 1',
      },
    ]
  },
}
</script>
