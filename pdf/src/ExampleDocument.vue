<template>
  <Document>
    <Page orientation="portrait">
      <Text :style="{ color: 'red' }">Hello world!</Text>
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
