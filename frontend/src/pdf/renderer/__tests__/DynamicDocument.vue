<template>
  <Document>
    <Page>
      <Text :render="({ pageNumber, totalPages }) => `${pageNumber} / ${totalPages}`" />
      <Text :render="() => $toc.marked ?? ''" />
      <View
        :render="
          ({ pageNumber }) => {
            $toc.marked = pageNumber
          }
        "
      />
    </Page>
  </Document>
</template>
<script>
export default {
  name: 'DynamicDocument',
}
</script>
