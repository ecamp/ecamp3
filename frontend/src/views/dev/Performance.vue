<template>
  <v-container fluid style="max-width: 1024px">
    <content-card title="Performance" toolbar>
      <v-data-table
        :headers="headers"
        :items="results"
        item-key="endpoint"
        hide-default-footer
        :items-per-page="-1"
      >
        <template #[`item.collection`]="{ item }"
          >{{ numberformat.format(item.collection) }} ms</template
        >
        <template #[`item.entity`]="{ item }"
          >{{ numberformat.format(item.entity) }} ms</template
        >
      </v-data-table>
    </content-card>
  </v-container>
</template>

<script>
export default {
  name: 'Controls',
  components: {},
  data: () => ({
    numberformat: new Intl.NumberFormat('de-CH', {
      style: 'decimal',
      minimumFractionDigits: 1,
      maximumFractionDigits: 1,
    }),
    results: [],
    headers: [
      { text: 'Endpoint', value: 'endpoint' },
      {
        text: 'Collection',
        value: 'collection',
        align: 'right',
        cellClass: 'tabular-nums',
      },
      { text: 'Entity', value: 'entity', align: 'right', cellClass: 'tabular-nums' },
    ],
  }),
  async mounted() {
    const root = await this.api.get()._meta.load

    for (const key of Object.keys(root)) {
      if (['config', '_meta', '_storeData', 'apiActions'].includes(key)) continue

      if (key.includes('auth') || key.includes('login') || key.includes('reset')) continue

      if (key.includes('user') || key.includes('profile') || key.includes('invitation'))
        continue

      // const collection = await root[key]()._meta.load
      // const entity = collection.items[0]._meta.self

      const collection = await this.api.href(root, key)

      const collectionStart = performance.now()
      const collectionData = await this.axios.get(collection, { baseURL: '/' })
      const collectionEnd = performance.now()

      if (collectionData.data._embedded.items[0]) {
        await this.axios.get(collectionData.data._embedded.items[0]._links.self.href, {
          baseURL: '/',
        })
      }
      const entityEnd = performance.now()

      this.results.push({
        endpoint: key,
        collection: collectionEnd - collectionStart,
        entity: entityEnd - collectionEnd,
      })
    }
  },
}
</script>

<style scoped lang="scss">
::v-deep() th[role='columnheader'].text-right {
  direction: rtl;
  span {
    direction: ltr;
  }
}
</style>
