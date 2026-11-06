<template>
  <View v-if="duration" class="content-node" :class="{ 'content-node-compact': compact }">
    <InstanceName :content-node="contentNode" :icon="mdiRun" />
    <Text>{{ duration }}</Text>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import { ysPhysicalActivityDuration } from '@/helpers/ysPhysicalActivityDuration.js'
import { mdiRun } from '@mdi/js'

export default {
  name: 'YSPhysicalActivityDuration',
  components: { InstanceName },
  extends: PdfComponent,
  inject: {
    scheduleEntry: { default: null },
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return { mdiRun }
  },
  computed: {
    duration() {
      return ysPhysicalActivityDuration(
        this.scheduleEntry?.ysPhysicalActivityDurationMinutes,
        this.$tc.bind(this)
      )
    },
  },
}
</script>
<style lang="react-pdf"></style>
