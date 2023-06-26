<template>
  <Page :id="id" size="A4" :bookmark="$tc('print.cover.title')" class="page">
    <Text class="cover-camp-name cover-center">{{ config.camp.name }}</Text>
    <Text class="cover-camp-title cover-center">
      {{ config.camp.title }}
    </Text>
    <Text class="cover-camp-motto cover-center">
      {{ config.camp.motto }}
    </Text>
    <Text v-if="config.camp.organizer" class="cover-camp-organizer cover-center">
      {{ config.camp.organizer }}
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
.cover-center {
  text-align: center;
}
.cover-camp-name {
  font-size: 15pt;
  font-weight: semibold;
  margin: 140pt 0 40pt;
}
.cover-camp-title {
  font-size: 38pt;
  font-weight: bold;
  margin-bottom: 28pt;
}
.cover-camp-motto {
  font-size: 20pt;
  margin-bottom: 23pt;
}
.cover-camp-organizer {
  font-size: 13pt;
}
</pdf-style>
