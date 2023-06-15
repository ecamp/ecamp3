<template>
  <Document>
    <Page orientation="portrait">
      <View :style="{ backgroundColor: '#dddddd', margin: '20pt' }">
        <Text :style="{ color: 'red' }">Hello world!</Text>
      </View>
    </Page>
  </Document>
</template>
<script>
export default {
  name: 'ExampleDocument',
}

if (import.meta.hot) {
  import.meta.hot.accept(() => {
    // Quick fix for hot reloading in the demo: Tell vite that reloading this component alone isn't enough.
    // It needs to also reload the importer of this component.
    import.meta.hot.invalidate()
  })
}
</script>
