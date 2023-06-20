<template>
  <Document>
    <template v-for="(content, idx) in config.contents">
      <component
        :is="components[content.type]"
        v-if="content.type in components"
        :id="`entry-${idx}`"
        :key="idx"
        :config="config"
        :content="content"
      ></component>
    </template>
  </Document>
</template>
<script>
import { Font } from './renderer/index.js'
import PdfComponent from '@/PdfComponent.js'
import OpenSans from '@/assets/fonts/OpenSans/OpenSans-Regular.ttf'
import OpenSansItalic from '@/assets/fonts/OpenSans/OpenSans-Italic.ttf'
import OpenSansSemiBold from '@/assets/fonts/OpenSans/OpenSans-SemiBold.ttf'
import OpenSansSemiBoldItalic from '@/assets/fonts/OpenSans/OpenSans-SemiBoldItalic.ttf'
import OpenSansBold from '@/assets/fonts/OpenSans/OpenSans-Bold.ttf'
import OpenSansBoldItalic from '@/assets/fonts/OpenSans/OpenSans-BoldItalic.ttf'
import Cover from '@/campPrint/cover/Cover.vue'
import TableOfContents from '@/campPrint/tableOfContents/TableOfContents.vue'
import Picasso from '@/campPrint/picasso/Picasso.vue'

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
        //Program,
        //Activity,
        //Story,
      }
    },
  },
}

const registerFonts = async () => {
  Font.register({
    family: 'OpenSans',
    fonts: [
      // For now it seems that only ttf is supported, not woff or woff2
      { src: OpenSans },
      { src: OpenSansSemiBold, fontWeight: 'semibold' },
      { src: OpenSansBold, fontWeight: 'bold' },
      { src: OpenSansItalic, fontStyle: 'italic' },
      { src: OpenSansSemiBoldItalic, fontWeight: 'semibold', fontStyle: 'italic' },
      { src: OpenSansBoldItalic, fontWeight: 'bold', fontStyle: 'italic' },
    ],
  })

  Font.registerEmojiSource({
    formag: 'png',
    url: '/twemoji/assets/72x72/',
  })

  return await Promise.all([
    Font.load({ fontFamily: 'OpenSans' }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 600 }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 700 }),
    Font.load({ fontFamily: 'OpenSans', fontStyle: 'italic' }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 600, fontStyle: 'italic' }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 700, fontStyle: 'italic' }),
  ])
}

export const prepare = async (config) => {
  return await registerFonts(config)
}
</script>
<pdf-style>
.page {
  font-family: OpenSans;
  padding: 30;
  font-size: 12;
  display: flex;
  flex-direction: column;
}
.h1 {
  font-size: 16;
  font-weight: semibold;
  margin-bottom: 4;
}
.h2 {
font-size: 14;
font-weight: semibold;
margin-bottom: 4;
}
.h3 {
font-size: 12;
font-weight: semibold;
margin-bottom: 4;
}
</pdf-style>
