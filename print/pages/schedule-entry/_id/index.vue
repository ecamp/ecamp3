<template>
  <v-row no-gutters>
    <v-col cols="12">
      Single Schedule Entry
      <program-schedule-entry :schedule-entry="scheduleEntry" />
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
    this.scheduleEntry = await this.$api
      .get()
      .scheduleEntries({ scheduleEntryId: this.$route.params.id })._meta.load
  },
}
</script>
