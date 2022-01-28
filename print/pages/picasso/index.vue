<template>
  <v-row no-gutters>
    <v-col cols="12">
      <picasso-landscape :period="period" :demo="isDemo" />
    </v-col>
  </v-row>
</template>

<script>
import PicassoLandscape from '../../components/PicassoLandscape.vue'

export default {
  components: {
    PicassoLandscape,
  },
  layout: 'no-vuetify',
  data() {
    return {
      config: {},
      pagedjs: '',
      period: {
        description: 'Demo',
        start: '2019-01-06',
        end: '2019-01-11',
      },
      isDemo: true,
    }
  },
  async fetch() {
    const query = this.$route.query

    if (query.period) {
      const url = new URL(query.period, process.env.INTERNAL_API_ROOT_URL)
      // TODO find a sustainable solution in development for converting the URL to one reachable from the print service
      url.protocol = 'http://'
      url.host = 'caddy'
      url.port = '3001'

      this.period = await this.$api.get(url.toString())._meta.load
      this.isDemo = false
    }
  },
}
</script>
