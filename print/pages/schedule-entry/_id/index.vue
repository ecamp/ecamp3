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
import cssesc from 'cssesc'

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
    header.__dangerouslyDisableSanitizersByTagID = {
      scheduleEntryMarginBox: ['cssText'], // disable sanitzing of below inline css
    }

    const campName = cssesc(this.camp.name)
    const activityTitle = cssesc(this.activity.title)

    header.style = [
      {
        type: 'text/css',
        hid: 'scheduleEntryMarginBox',
        cssText: `@media print {
                  
                    @page {

                      @top-left {
                        content: '${campName}';
                        font-size: var(--ecamp-margin-font-size);
                      }

                      @top-center {
                        content: '${activityTitle}';
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
