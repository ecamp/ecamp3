<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <div>
    <v-btn
      class="ml-3"
      color="primary"
      @click="print">
      <v-icon left>mdi-printer</v-icon>
      Print
    </v-btn>

    <v-btn
      class="ml-3"
      color="primary"
      outlined
      @click="reload">
      <v-icon left>mdi-refresh</v-icon>
      Reload
    </v-btn>

    <v-btn
      class="ml-3"
      color="primary"
      outlined
      :href="previewUrl"
      target="_blank">
      <v-icon left>mdi-open-in-new</v-icon>
      Open preview in new window
    </v-btn>

    <iframe
      v-if="!scheduleEntry()._meta.loading"
      id="previewIFrame"
      ref="previewIFrame"
      name="previewIFrame"
      class="mt-3"
      frameborder="1"
      width="100%"
      height="1150"
      :src="previewUrl" />
  </div>
</template>

<script>
const PRINT_SERVER = window.environment.PRINT_SERVER

export default {
  name: 'PrintActivity',
  components: {
  },
  props: {
    scheduleEntry: { type: Function, required: true }
  },
  data () {
    return {}
  },
  computed: {
    previewUrl () {
      return `${PRINT_SERVER}/schedule-entry/${this.scheduleEntry().id}?pagedjs=true&lang=${this.lang}`
    },
    lang () {
      return this.$store.state.lang.language
    }
  },
  methods: {
    print () {
      // send print event to iFrame
      this.$refs.previewIFrame.contentWindow.postMessage(
        {
          event_id: 'print'
        },
        '*'
      )
    },
    reload () {
      // send reload event to iFrame
      this.$refs.previewIFrame.contentWindow.postMessage(
        {
          event_id: 'reload'
        },
        '*'
      )
    }

  }
}
</script>

<style scoped>
  iframe {
    border: 1px black solid;
  }
</style>
