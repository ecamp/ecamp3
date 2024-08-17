<template>
  <Page :id="id" size="A4" :bookmark="$tc('print.cover.title')" class="page">
    <View class="cover-camp-wrapper">
      <Text v-if="config.camp.organizer" class="cover-camp-organizer cover-center">
        {{ config.camp.organizer }}
      </Text>
      <Text class="cover-camp-title cover-center">
        {{ config.camp.title }}</Text>
      <Text class="cover-camp-motto cover-center">
        {{ config.camp.motto }}
      </Text>
    </View>
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
.cover-camp-wrapper {
  padding: 72pt 0 0;
}
.cover-camp-organizer {
  font-size: 18pt;
  font-weight: medium;
}
.cover-camp-title {
  font-size: 38pt;
  font-weight: semibold;
  margin: 40pt 0;
}
.cover-camp-motto {
  font-size: 28pt;
  font-weight: medium;
}
</pdf-style>
