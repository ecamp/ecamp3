<!--
Print preview for single Activity
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

    <div v-if="!done" class="my-3">
      {{ loadedPagesThrottled }} pages loaded
      <v-progress-linear
        :value="progress"
        :indeterminate="progress === 0" />
    </div>

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
import { throttle } from 'lodash'

const PRINT_SERVER = window.environment.PRINT_SERVER

export default {
  name: 'PrintActivity',
  components: {
  },
  props: {
    scheduleEntry: { type: Function, required: true }
  },
  data () {
    return {
      done: false,
      loadedPages: 0,
      loadedPagesThrottled: 0
    }
  },
  computed: {
    progress () {
      if (this.done) {
        return 100
      }
      const n = 6
      return Math.round(Math.pow(this.loadedPagesThrottled / (this.loadedPagesThrottled + 1), n) * 100)
    },
    previewUrl () {
      return `${PRINT_SERVER}/schedule-entry/${this.scheduleEntry().id}?pagedjs=true&lang=${this.lang}`
    },
    lang () {
      return this.$store.state.lang.language
    }
  },
  watch: {
    loadedPages: function (val) {
      if (val === 0) {
        this.loadedPagesThrottled = val
      } else {
        this.throttler()
      }
    }
  },
  mounted () {
    // listen on messages from iFrame
    window.addEventListener('message', this.receiveMessage)
  },
  beforeDestroy () {
    window.removeEventListener('message', this.receiveMessage)
  },
  methods: {

    throttler: throttle(function () {
      this.loadedPagesThrottled = this.loadedPages
    }, 100),

    receiveMessage (event) {
      if (event.origin !== PRINT_SERVER) { return }

      if (event.data) {
        // message from iFrame: 1 more page is rendered
        if (event.data.event_id === 'pagedjs_progress') {
          this.loadedPages = event.data.page + 1

        // message from iFrame: preview finished
        } else if (event.data.event_id === 'pagedjs_done') {
          this.done = true
        }
      }
    },
    print () {
      // send print event to iFrame
      this.$refs.previewIFrame.contentWindow.postMessage(
        {
          event_id: 'print'
        },
        PRINT_SERVER
      )
    },
    reload () {
      this.done = false
      this.loadedPages = 0

      // send reload event to iFrame
      this.$refs.previewIFrame.contentWindow.postMessage(
        {
          event_id: 'reload'
        },
        PRINT_SERVER
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
