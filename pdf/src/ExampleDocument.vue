<template>
  <Document>
    <Page orientation="portrait">
      <View :style="{ backgroundColor: '#dddddd', margin: '20pt' }">
        <Text :style="{ color: 'red' }"
          >{{ $tc('print.cover.title') }} {{ displayText }}
          {{ firstConfiguredPeriodDescription }}</Text
        >
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
    displayText() {
      return 'Hello world! ' + this.config.camp.name
    },
    firstConfiguredPeriodDescription() {
      const periodUri = this.config.contents.find((content) => content.type === 'Picasso')
        .options.periods[0]
      return this.api.get(periodUri).description
    },
  },
}
</script>
