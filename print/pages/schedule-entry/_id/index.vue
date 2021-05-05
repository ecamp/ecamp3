<template>
  <v-row no-gutters>
    <v-col cols="12">
      <error v-if="$fetchState.error">{{ $fetchState.error.message }}</error>
      <div v-else-if="!$fetchState.pending">
        <program-schedule-entry :schedule-entry="scheduleEntry" />
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  data() {
    return {
      config: {},
      scheduleEntry: null,
      activity: null,
      camp: null,
    }
  },
  async fetch() {
    try {
      this.scheduleEntry = await this.$api
        .get()
        .scheduleEntries({ scheduleEntryId: this.$route.params.id })._meta.load

      this.activity = await this.scheduleEntry.activity()._meta.load
      this.camp = await this.activity.camp()._meta.load
    } catch (e) {
      this.$nuxt.context.res.statusCode = 404
      throw new Error('Schedule Entry not found')
    }
  },
  head() {
    const header = {}

    /**
     * Footer & header for single activity view
     */
    header.__dangerouslyDisableSanitizers = ['style'] // disable sanitzing of below inline css
    header.style = [
      {
        type: 'text/css',
        cssText: `@media print {
                  
                    @page {

                      @top-left {
                        content: '${this.camp.name}';
                        font-size: var(--ecamp-margin-font-size);
                      }

                      @top-center {
                        content: '${this.activity.title}';
                        font-size: var(--ecamp-margin-font-size);
                      }
                    }
                  }`,
      },
    ]

    return header
  },
}
</script>
