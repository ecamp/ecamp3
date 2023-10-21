<template>
  <!-- <v-row no-gutters>
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
  </v-row> -->
  <div>{{ camp }}</div>
</template>

<script>
export default defineNuxtComponent({
  fetchKey: 'root',
  async asyncData() {
    const { $api } = useNuxtApp()
    const route = useRoute()

    const query = route.query
    const config = JSON.parse(query.config || '{}')

    console.log(config)

    return {
      camp: await $api.get(config.camp)._meta.load, // TODO prevent specifying arbitrary absolute URLs that the print container should fetch...
      config,
    }
  },
})
</script>

<style>
:root {
  --tw-prose-body: #000 !important;
}

body {
  color: #000;
  font-family: 'InterDisplay', Helvetica Neue, Helvetica, Arial, sans-serif;
  font-size: 10pt;
}

.tw-prose ol {
  padding-left: 16px;
}

.tw-prose li ::marker {
  color: #000;
}
</style>
