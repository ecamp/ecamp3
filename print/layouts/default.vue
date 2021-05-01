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
          {
            src: '/pagedConfig.js',
          },
          {
            src: 'https://unpkg.com/pagedjs/dist/paged.polyfill.js',
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

  mounted() {
    /**
     * Register event listeners to listen to iFrame ancestor postMessages
     */
    window.addEventListener('message', (event) => {
      if (event.data) {
        if (event.data.event_id === 'reload') {
          window.location.reload()
        } else if (event.data.event_id === 'print') {
          window.print()
        }
      }
    })
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
