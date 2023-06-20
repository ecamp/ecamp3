<template>
  <Page :id="id" size="A4" :bookmark="$tc('print.cover.title')" class="page">
    <Text class="cover-camp-name">{{ config.camp.name }}</Text>
    <Text class="cover-camp-metadata">
      <Text style="font-weight: bold">{{ $tc('entity.camp.fields.title') }}: </Text>
      {{ config.camp.title }}
    </Text>
    <Text class="cover-camp-metadata">
      <Text style="font-weight: bold">{{ $tc('entity.camp.fields.motto') }}: </Text>
      {{ config.camp.motto }}
    </Text>
  </Page>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'

export default {
  name: 'Cover',
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true },
  },
}
</script>
<pdf-style>
.cover-camp-name {
  text-align: center;
  margin: 40pt 0;
  font-size: 40pt;
}
.cover-camp-metadata {
  text-align: center;
  margin-bottom: 25pt;
  font-size: 20pt;
}
</pdf-style>
