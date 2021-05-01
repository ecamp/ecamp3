<template>
  <v-row no-gutters>
    <v-col cols="12">
      <error v-if="$fetchState.error">{{ $fetchState.error.message }}</error>
      <div v-else-if="!$fetchState.pending">
        Single Schedule Entry
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
    }
  },
  async fetch() {
    try {
      this.scheduleEntry = await this.$api
        .get()
        .scheduleEntries({ scheduleEntryId: this.$route.params.id })._meta.load
    } catch (e) {
      this.$nuxt.context.res.statusCode = 404
      throw new Error('Schedule Entry not found')
    }
  },
}
</script>
