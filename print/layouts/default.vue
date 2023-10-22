<template>
  <div class="container" fluid>
    <slot />
  </div>
</template>

<script setup>
const { t, locale } = useI18n({
  useScope: 'global',
})

const header = {}

header.htmlAttrs = {
  moznomarginboxes: true,
  mozdisallowselectionprint: true,
  lang: locale,
}

/**
 * Define default footer & header
 * This can be overridden in route views
 */
header.__dangerouslyDisableSanitizersByTagID = {
  defaultMarginBox: ['cssText'], // disable sanitzing of below inline css
}

const cssPageCounter = `'${t('global.margin.pageCounter.page')} ' counter(page) ' ${t(
  'global.margin.pageCounter.of'
)}  ' counter(pages)`

header.style = [
  {
    type: 'text/css',
    hid: 'defaultMarginBox',
    cssText: `@media print {

                    :root {
                      --ecamp-margin-font-size: 10pt;
                    }

                    @page {
                      font-family: "Helvetica", sans-serif;
                      size: A4 portrait;              
                      margin-bottom: 15mm;
                      margin-left: 15mm;
                      margin-right: 15mm;
                      margin-top: 15mm;

                      @top-center {
                        content: 'eCamp3';
                        font-size: var(--ecamp-margin-font-size);
                      }
                      @bottom-center {
                        content: ${cssPageCounter};
                        font-size: var(--ecamp-margin-font-size);
                      }
                    }
                  }`,
  },
]

header.script = []

// inject FRONTEND_URL to client
// header.__dangerouslyDisableSanitizersByTagID.environmentVariables = ['innerHTML']
// header.script.push({
//   hid: 'environmentVariables',
//   type: 'application/javascript',
//   innerHTML: `window.FRONTEND_URL = '${process.env.FRONTEND_URL}'`,
// })

header.link = [
  {
    rel: 'stylesheet',
    href: './fonts/inter/inter.css',
  },
]

useHead(header)
</script>

<style lang="scss" scoped>
.container {
  margin: 0;
  padding: 0;
}
</style>
