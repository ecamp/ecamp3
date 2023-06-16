<template>
  <Document>
    <Page orientation="portrait">
      <View :style="{ backgroundColor: '#dddddd', margin: '20pt' }">
        <Text :style="{ color: 'red' }"
          >{{ $tc('print.toc.title') }} {{ config.camp.name }}</Text
        >
        <View v-for="period in selectedPeriods" :key="period._meta.self">
          <Text>{{ period.description }}</Text>
        </View>
      </View>
    </Page>
  </Document>
</template>
<script>
export default {
  name: 'ExampleDocument',
  props: {
    config: { type: Object, required: true },
  },
  computed: {
    selectedPeriods() {
      return this.config.contents
        .flatMap((content) => {
          return content?.options?.periods || []
        })
        .map((periodUri) => {
          return this.api.get(periodUri)
        })
    },
  },
}
</script>
