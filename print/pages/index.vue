<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div v-for="(content, idx) in config.contents" :key="idx">
        <component
          :is="'Config' + content.type"
          :options="content.options"
          :camp="camp"
          :config="config"
          :index="idx"
        />
      </div>
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

    this.config = JSON.parse(query.config || '{}')

    try {
      this.camp = await this.$api.get(this.config.camp)._meta.load // TODO prevent specifying arbitrary absolute URLs that the print container should fetch...
    } catch (error) {
      // eslint-disable-next-line
      console.log(error)
    }
  },
}
</script>
