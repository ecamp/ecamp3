<template>
  <Document pdf-version="1.7">
    <template v-for="(content, idx) in config.contents">
      <component
        :is="components[content.type]"
        v-if="content.type in components"
        :id="`entry-${idx}`"
        :config="config"
        :content="content"
      ></component>
    </template>
  </Document>
</template>
<script>
import { Font } from './renderer/index.js'
import PdfComponent from '@/PdfComponent.js'
import InterDisplay from '@/assets/fonts/Inter/InterDisplay-Regular.ttf'
import InterDisplayItalic from '@/assets/fonts/Inter/InterDisplay-Italic.ttf'
import InterDisplaySemiBold from '@/assets/fonts/Inter/InterDisplay-SemiBold.ttf'
import InterDisplayBold from '@/assets/fonts/Inter/InterDisplay-Bold.ttf'
import InterDisplayBoldItalic from '@/assets/fonts/Inter/InterDisplay-BoldItalic.ttf'
import Cover from '@/campPrint/cover/Cover.vue'
import TableOfContents from '@/campPrint/tableOfContents/TableOfContents.vue'
import Picasso from '@/campPrint/picasso/Picasso.vue'
import Story from '@/campPrint/story/Story.vue'
import Program from '@/campPrint/program/Program.vue'
import Activity from '@/campPrint/activity/Activity.vue'
import { wordHyphenation } from '@react-pdf/textkit'

const originalHyphenationCallback = wordHyphenation()

export default {
  name: 'CampPrint',
  extends: PdfComponent,
  props: {
    config: { type: Object, required: true },
  },
  computed: {
    components() {
      return {
        Cover,
        Toc: TableOfContents,
        Picasso,
        Program,
        Activity,
        Story,
      }
    },
  },
}

const registerFonts = async () => {
  Font.registerHyphenationCallback((word) => {
    if (word && word.length > 70) {
      return word.split('')
    }
    return originalHyphenationCallback(word)
  })

  Font.register({
    family: 'InterDisplay',
    fonts: [
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
.page {
  font-family: InterDisplay;
  padding: 30;
  font-size: 12;
  display: flex;
  flex-direction: column;
}
.h1 {
  font-size: 16;
  font-weight: semibold;
  margin: 12pt 0 3pt;
}
.h2 {
  font-size: 14;
  font-weight: semibold;
  margin: 10pt 0 3pt;
}
.h3 {
  font-size: 12;
  font-weight: semibold;
  margin: 8pt 0 3pt;
}
</pdf-style>
