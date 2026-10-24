<template>
  <View v-if="duration" class="content-node">
    <InstanceName :content-node="contentNode" />
    <Text>{{ duration }}</Text>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import { ysPhysicalActivityDurationLong } from '@/common/helpers/ysPhysicalActivityDuration.js'

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
  computed: {
    duration() {
      return ysPhysicalActivityDurationLong(
        this.scheduleEntry?.ysPhysicalActivityDurationMinutes,
        this.$tc.bind(this)
      )
    },
  },
}
</script>
<style lang="react-pdf"></style>
