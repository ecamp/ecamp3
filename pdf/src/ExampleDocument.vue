<template>
  <Document>
    <Page orientation="portrait">
      <View :style="{ backgroundColor: '#dddddd', margin: '20pt' }">
        <Text :style="{ color: 'red' }">{{ displayText }}</Text>
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
  },
}
</script>
