<template>
  <v-layout column justify-center align-center>
    <v-flex xs12 sm8 md6>
      <h1>Table of content</h1>
      <h2 v-for="activity in activities" :key="activity.id">
        {{ activity.title }}
      </h2>
    </v-flex>
  </v-layout>
</template>

<script>
export default {
  async asyncData({ query, $axios }) {
    const { data } = await $axios.get(`/activities?campId=${query.camp}`)

    return {
      query,
      activities: data._embedded.items,
    }
  },
  head() {
    if (this.query.pagedjs === 'true') {
      return {
        script: [
          {
            src: 'https://unpkg.com/pagedjs/dist/paged.polyfill.js',
          },
        ],
      }
    }
  },
}
</script>
