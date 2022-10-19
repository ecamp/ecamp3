<template>
  <v-row no-gutters>
    <v-col cols="12">
      <generic-error-message v-if="$fetchState.error" :error="$fetchState.error" />
      <div v-for="(content, idx) in config.contents" v-else :key="idx">
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
      camp: null,
    }
  },
  async fetch() {
    const query = this.$route.query
    this.config = JSON.parse(query.config || '{}')
    this.camp = await this.$api.get(this.config.camp)._meta.load // TODO prevent specifying arbitrary absolute URLs that the print container should fetch...
  },
}
</script>
