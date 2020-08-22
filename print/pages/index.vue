<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="TOC">
        <h1>Table of content</h1>
        <p v-for="activity in activities" :key="'toc_' + activity.id">
          <a class="link" :href="'#activity_' + activity.id">
            {{ activity.title }}
          </a>
        </p>
      </div>

      <div
        v-for="activity in activities"
        :key="'activity_' + activity.id"
        class="event"
      >
        <h2 :id="'activity_' + activity.id">
          {{ activity.title }}
        </h2>
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  async asyncData({ query, $axios }) {
    const { data } = await $axios.get(`/activities?campId=${query.camp}`)

    return {
      query,
      activities: data._embedded.items,
    }
  },
  head() {
    if (this.query.pagedjs === 'true') {
      return {
        script: [
          {
            src: 'https://unpkg.com/pagedjs/dist/paged.polyfill.js',
          },
        ],
        link: [
          {
            rel: 'stylesheet',
            href: 'print-preview.css',
          },
        ],
      }
    }
  },
}
</script>

<style lang="scss" scoped>
@media print {
  .TOC {
    page-break-after: always;
  }

  .link::after {
    content: ', page ' target-counter(attr(href url), page);
  }

  .event {
    page-break-after: always;
  }

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
