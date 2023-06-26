<template>
  <Document>
    <Page orientation="portrait" style="font-family: InterDisplay">
      <View class="container">
        <Text style="color: red"
          >{{ $tc('print.toc.title') }} {{ config.camp.name }}</Text
        >
        <View v-for="period in selectedPeriods">
          <Text class="green bold">{{ period.description }}</Text>
          <SubComponent />
        </View>
      </View>
    </Page>
  </Document>
</template>
<script>
import { Font } from './renderer/index.js'
import SubComponent from './SubComponent.vue'
import PdfComponent from '@/PdfComponent.js'
import InterDisplay from '@/assets/fonts/Inter/InterDisplay-Regular.ttf'
import InterDisplayItalic from '@/assets/fonts/Inter/InterDisplay-Italic.ttf'
import InterDisplaySemiBold from '@/assets/fonts/Inter/InterDisplay-SemiBold.ttf'
import InterDisplayBold from '@/assets/fonts/Inter/InterDisplay-Bold.ttf'
import InterDisplayBoldItalic from '@/assets/fonts/Inter/InterDisplay-BoldItalic.ttf'

export default {
  name: 'ExampleDocument',
  components: { SubComponent },
  extends: PdfComponent,
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

const registerFonts = async () => {
  Font.register({
    family: 'InterDisplay',
    fonts: [
      // For now it seems that only ttf is supported, not woff or woff2
      { src: InterDisplay },
      { src: InterDisplaySemiBold, fontWeight: 'semibold' },
      { src: InterDisplayBold, fontWeight: 'bold' },
      { src: InterDisplayItalic, fontStyle: 'italic' },
      { src: InterDisplayBoldItalic, fontWeight: 'bold', fontStyle: 'italic' },
    ],
  })

  Font.registerEmojiSource({
    formag: 'png',
    url: '/twemoji/assets/72x72/',
  })

  return await Promise.all([
    Font.load({ fontFamily: 'InterDisplay' }),
    Font.load({ fontFamily: 'InterDisplay', fontWeight: 600 }),
    Font.load({ fontFamily: 'InterDisplay', fontWeight: 700 }),
    Font.load({ fontFamily: 'InterDisplay', fontStyle: 'italic' }),
    Font.load({ fontFamily: 'InterDisplay', fontWeight: 600, fontStyle: 'italic' }),
    Font.load({ fontFamily: 'InterDisplay', fontWeight: 700, fontStyle: 'italic' }),
  ])
}

export const prepare = async (config) => {
  return await registerFonts(config)
}
</script>
<pdf-style>
.container {
  background-color: #dddddd;
  margin: 20pt;
}
.green {
  color: green;
}
.bold {
  font-weight: bold;
}
</pdf-style>
