<template>
  <v-row no-gutters>
    <v-col cols="12">
      <picasso-landscape :period="period" :demo="isDemo" />
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
      this.period = await this.$api.get(query.period)._meta.load
      this.isDemo = false
    }
  },
}
</script>
