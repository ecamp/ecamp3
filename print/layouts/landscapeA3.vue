<template>
  <v-app>
    <v-main>
      <v-container class="container" fluid>
        <nuxt />
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  layout: 'landscapeA3',
  data() {
    return {
      pagedjs: false,
    }
  },

  head() {
    const header = {}

    header.htmlAttrs = {
      moznomarginboxes: true,
      mozdisallowselectionprint: true,
    }

    /**
     * Define default footer & header
     * This can be overridden in route views
     */
    header.__dangerouslyDisableSanitizersByTagID = {
      defaultMarginBox: ['cssText'], // disable sanitzing of below inline css
    }

    const cssPageCounter = `'${this.$tc(
      'global.margin.pageCounter.page'
    )} ' counter(page) ' ${this.$tc(
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
                      font-family: "Open Sans", sans-serif;
                      size: A3 landscape;
                      margin: 15mm 15mm 15mm 15mm;

                      @top-center {
                        content: 'eCamp3';
                        font-size: var(--ecamp-margin-font-size);
                      }
                      @bottom-center {
                        content: ${cssPageCounter};
                        font-size: var(--ecamp-margin-font-size);
                      }
                    }

                    .toclink::after {
                      content: ', page ' target-counter(attr(href url), page);
                    }
                  }`,
      },
    ]

    header.script = []

    // inject FRONTEND_URL to client
    header.__dangerouslyDisableSanitizersByTagID.environmentVariables = [
      'innerHTML',
    ]
    header.script.push({
      hid: 'environmentVariables',
      type: 'application/javascript',
      innerHTML: `window.FRONTEND_URL = '${process.env.FRONTEND_URL}'`,
    })

    if (this.$route.query.pagedjs === 'true') {
      // confiugration JS for pagedJS
      header.script.push({
        src: '/pagedConfig.js',
      })

      // PagedJS
      header.script.push({
        src: 'https://unpkg.com/pagedjs/dist/paged.polyfill.js',
      })

      // event listener to communicate with parent when embedded in iFrame
      header.script.push({
        src: '/iframeEvents.js',
      })

      header.link = [
        {
          rel: 'stylesheet',
          href: '/print-preview.css',
        },
      ]
    }

    return header
  },
}
</script>

<style lang="scss" scoped>
.container {
  margin: 0;
  padding: 0;
}

// @media print {
//   @page {
//     size: a4 portrait;
//   }
// }
</style>
