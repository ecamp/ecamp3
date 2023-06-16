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
  const [
    OpenSans,
    OpenSansItalic,
    OpenSansSemiBold,
    OpenSansSemiBoldItalic,
    OpenSansBold,
    OpenSansBoldItalic,
  ] = (
    await Promise.all([
      import('../public/fonts/OpenSans/OpenSans-Regular.ttf'),
      import('../public/fonts/OpenSans/OpenSans-Italic.ttf'),
      import('../public/fonts/OpenSans/OpenSans-SemiBold.ttf'),
      import('../public/fonts/OpenSans/OpenSans-SemiBoldItalic.ttf'),
      import('../public/fonts/OpenSans/OpenSans-Bold.ttf'),
      import('../public/fonts/OpenSans/OpenSans-BoldItalic.ttf'),
    ])
  ).map((module) => module.default)

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
