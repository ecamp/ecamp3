<!--
This component renders nothing visibly in the document, but keeps
track of the page number it's located on, for generating the table
of contents.
-->
<template>
  <View
    :render="
      ({ pageNumber }) => {
        $toc[id] = pageNumber
      }
    "
  />
</template>
<script>
export default {
  name: 'TocSectionStartMarker',
  props: {
    id: { type: String, required: true },
  },
}
</script>
