<template>
  <content-node-content v-if="duration" :content-node="contentNode" :icon-path="mdiRun">
    {{ duration }}
  </content-node-content>
</template>

<script>
import ContentNodeContent from './ContentNodeContent.vue'
import { mdiRun } from '@mdi/js'
import { ysPhysicalActivityDuration } from '@/common/helpers/ysPhysicalActivityDuration.js'

export default {
  components: {
    ContentNodeContent,
  },
  inject: {
    scheduleEntry: { default: null },
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return {
      mdiRun,
    }
  },
  computed: {
    duration() {
      return ysPhysicalActivityDuration(
        this.scheduleEntry?.ysPhysicalActivityDurationMinutes,
        this.$t.bind(this)
      )
    },
  },
}
</script>
