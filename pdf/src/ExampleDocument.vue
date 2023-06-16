<template>
  <Document>
    <Page orientation="portrait" :style="{ fontFamily: 'OpenSans' }">
      <View :style="{ backgroundColor: '#dddddd', margin: '20pt' }">
        <Text :style="{ color: 'red' }"
          >{{ $tc('print.toc.title') }} {{ config.camp.name }}</Text
        >
        <View v-for="period in selectedPeriods" :key="period._meta.self">
          <Text>{{ period.description }}</Text>
        </View>
      </View>
    </Page>
  </Document>
</template>
<script>
import { Font } from './renderer/index.js'
import OpenSans from '@/assets/fonts/OpenSans/OpenSans-Regular.ttf'
import OpenSansItalic from '@/assets/fonts/OpenSans/OpenSans-Italic.ttf'
import OpenSansSemiBold from '@/assets/fonts/OpenSans/OpenSans-SemiBold.ttf'
import OpenSansSemiBoldItalic from '@/assets/fonts/OpenSans/OpenSans-SemiBoldItalic.ttf'
import OpenSansBold from '@/assets/fonts/OpenSans/OpenSans-Bold.ttf'
import OpenSansBoldItalic from '@/assets/fonts/OpenSans/OpenSans-BoldItalic.ttf'

export default {
  name: 'ExampleDocument',
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
