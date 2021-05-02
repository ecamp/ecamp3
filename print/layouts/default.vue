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
    if (this.$route.query.pagedjs === 'true') {
      return {
        script: [
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
        ],
        link: [
          {
            rel: 'stylesheet',
            href: '/print-preview.css',
          },
        ],
      }
    }
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

    @top-left {
      content: 'eCamp3';
    }

    @top-center {
      content: 'Placeholder Lagertitel';
    }
    @bottom-left {
      content: counter(page) ' of ' counter(pages);
    }
  }
}
</style>
