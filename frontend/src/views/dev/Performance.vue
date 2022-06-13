<template>
  <v-container fluid>
    <content-card title="Config">
      <v-card-text>
        <table class="result-table">
          <tr>
            <th>Endpoint</th>
            <th>Collection ms</th>
            <th>Entity ms</th>
          </tr>
          <tr v-for="result in results" :key="result.endpoint">
            <td>{{ result.endpoint }}</td>
            <td class="text-right">
              {{ result.collection }}
            </td>
            <td class="text-right">
              {{ result.entity }}
            </td>
          </tr>
        </table>
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
export default {
  name: 'Controls',
  components: {},
  data: () => ({
    results: [],
  }),
  computed: {},
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

      const collectionStart = new Date().getTime()
      const collectionData = await this.axios.get(collection)
      const collectionEnd = new Date().getTime()

      await this.axios.get(collectionData.data._embedded.items[0]._links.self.href)
      const entityEnd = new Date().getTime()

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
.result-table {
  border: 1px black solid;
  border-collapse: collapse;
  tr {
    td,
    th {
      border: 1px black solid;
      padding: 5px;
    }
  }
}
</style>
