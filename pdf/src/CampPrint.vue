<template>
  <Document pdf-version="1.7">
    <template v-for="(content, idx) in config.contents">
      <component
        :is="components[content.type]"
        v-if="content.type in components"
        :id="`entry-${idx}`"
        :index="idx"
        :total-contents="config.contents.length"
        :type="content.type"
        :config="config"
        :content="content"
      >
        <Text
          v-if="config.options.pageNumbers"
          :render="({ pageNumber, totalPages }) => `${pageNumber} / ${totalPages}`"
          fixed
          class="page-number"
        />
      </component>
    </template>
  </Document>
</template>
<script>
import { Font } from './renderer/index.js'
import PdfComponent from '@/PdfComponent.js'
import InterDisplay from '@/assets/fonts/Inter/InterDisplay-Regular.ttf'
import InterDisplayItalic from '@/assets/fonts/Inter/InterDisplay-Italic.ttf'
import InterDisplayMedium from '@/assets/fonts/Inter/InterDisplay-Medium.ttf'
import InterDisplaySemiBold from '@/assets/fonts/Inter/InterDisplay-SemiBold.ttf'
import InterDisplayBold from '@/assets/fonts/Inter/InterDisplay-Bold.ttf'
import InterDisplayBoldItalic from '@/assets/fonts/Inter/InterDisplay-BoldItalic.ttf'
import Cover from '@/campPrint/cover/Cover.vue'
import TableOfContents from '@/campPrint/tableOfContents/TableOfContents.vue'
import Picasso from '@/campPrint/picasso/Picasso.vue'
import Story from '@/campPrint/summary/Story.vue'
import SafetyConsiderations from '@/campPrint/summary/SafetyConsiderations.vue'
import Program from '@/campPrint/program/Program.vue'
import Activity from '@/campPrint/activity/Activity.vue'
import ActivityList from '@/campPrint/activityList/ActivityList.vue'
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
        SafetyConsiderations,
        ActivityList,
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
      { src: InterDisplayMedium, fontWeight: 'medium' },
      { src: InterDisplaySemiBold, fontWeight: 'semibold' },
      { src: InterDisplayBold, fontWeight: 'bold' },
      { src: InterDisplayItalic, fontStyle: 'italic' },
      { src: InterDisplayBoldItalic, fontWeight: 'bold', fontStyle: 'italic' },
    ],
  })

  Font.registerEmojiSource({
    withVariationSelectors: true,
    builder(code) {
      // If the code point does not contain 200d, remove any fe0f
      // https://github.com/twitter/twemoji/issues/419#issuecomment-637360325
      const filename = code.includes('200d')
        ? code
        : code
            .split('-')
            .filter((part) => part && part !== 'fe0f')
            .join('-')
      return '/twemoji/assets/72x72/' + filename + '.png'
    },
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
<style lang="react-pdf">
.page {
  font-family: InterDisplay;
  padding: 30pt;
  font-size: 12pt;
  display: flex;
  flex-direction: column;
}
.h1 {
  font-size: 16pt;
  font-weight: 600;
  margin: 12pt 0 3pt;
}
.h2 {
  font-size: 14pt;
  font-weight: 600;
  margin: 10pt 0 3pt;
}
.h3 {
  font-size: 12pt;
  font-weight: 600;
  margin: 8pt 0 3pt;
}
.page-number {
  position: absolute;
  bottom: 15pt;
  left: 0;
  right: 0;
  width: 100%;
  text-align: center;
  font-size: 10pt;
}
</style>
