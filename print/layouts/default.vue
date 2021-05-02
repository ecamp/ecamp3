<template>
  <v-app>
    <v-main>
      <v-container class="container">
        <nuxt />
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
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
    header.__dangerouslyDisableSanitizers = ['style'] // disable sanitzing of below inline css

    const cssPageCounter = `'${this.$tc(
      'global.margin.pageCounter.page'
    )} ' counter(page) ' ${this.$tc(
      'global.margin.pageCounter.of'
    )}  ' counter(pages)`

    header.style = [
      {
        type: 'text/css',
        cssText: `@media print {
                    
                    :root {
                      --ecamp-margin-font-size: 10pt;
                    }

                    @page {
                      font-family: "Roboto", sans-serif;
                      
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

    if (this.$route.query.pagedjs === 'true') {
      header.script = [
        // confiugration JS for pagedJS
        {
          src: '/pagedConfig.js',
        },

        // PagedJS
        {
          src: 'https://unpkg.com/pagedjs/dist/paged.polyfill.js',
        },

        // event listener to communicate with parent when embedded in iFrame
        {
          src: '/iframeEvents.js',
        },
      ]

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

@media print {
  @page {
    size: a4 portrait;
  }
}
</style>
