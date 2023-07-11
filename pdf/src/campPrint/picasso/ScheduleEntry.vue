<template>
  <View
    class="picasso-schedule-entry"
    :style="{ backgroundColor: color, color: textColor }"
  >
    <View class="picasso-schedule-entry-spacer" />
    <Text class="picasso-schedule-entry-title">
      {{ category }} {{ scheduleEntry.number }} {{ title }}
    </Text>
    <View class="picasso-schedule-entry-spacer" />
    <View class="picasso-schedule-entry-responsibles-container">
      <View class="picasso-schedule-entry-spacer" />
      <Responsibles
        class="picasso-schedule-entry-responsibles"
        :activity="scheduleEntry.activity()"
        avatars
      />
      <View class="picasso-schedule-entry-spacer" />
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import Responsibles from '../Responsibles.vue'
import { contrastColor } from '../../../common/helpers/colors.js'

export default {
  name: 'ScheduleEntry',
  components: { Responsibles },
  extends: PdfComponent,
  props: {
    scheduleEntry: { type: Object, required: true },
  },
  computed: {
    color() {
      return this.scheduleEntry.activity().category().color
    },
    textColor() {
      return contrastColor(this.color)
    },
    category() {
      return this.scheduleEntry.activity().category().short
    },
    title() {
      return this.scheduleEntry.activity().title
    },
  },
}
</script>
<pdf-style>
.picasso-schedule-entry {
  position: absolute;
  padding: 0 4pt;
  flex-direction: column;
  justify-content: flex-start;
}
.picasso-schedule-entry-spacer {
  height: 0;
  max-height: 4pt;
  flex-grow: 1;
}
.picasso-schedule-entry-title {
  fontSize: 8pt;
  height: 16pt;
  line-height: 1;
  flex-grow: 1;
}
.picasso-schedule-entry-responsibles-container {
  position: absolute;
  top: 0;
  bottom: 0;
  right: 0;
  left: 0;
  flex-direction: column;
  align-items: flex-end;
  justify-content: flex-end;
  padding: 0 4pt;
}
</pdf-style>
