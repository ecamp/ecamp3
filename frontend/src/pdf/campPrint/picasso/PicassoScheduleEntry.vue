<template>
  <View
    class="picasso-schedule-entry"
    :class="{ 'picasso-schedule-entry-compact': compact }"
    :style="{ backgroundColor: color }"
  >
    <View
      class="picasso-schedule-entry-spacer"
      :class="{ 'picasso-schedule-entry-spacer-compact': compact }"
    />
    <Link class="picasso-schedule-entry-link" :href="linkTarget">
      <Text
        class="picasso-schedule-entry-title"
        :style="{ fontSize: fontSize, color: textColor }"
      >
        {{ category }}
        <Text class="tabular-nums">{{ scheduleEntry.number }}</Text>
        {{ title }}{{ ysPhysicalActivityDurationAfterTitle }}
      </Text>
    </Link>
    <View
      class="picasso-schedule-entry-spacer"
      :class="{ 'picasso-schedule-entry-spacer-compact': compact }"
    />
    <View
      v-if="showYSPhysicalActivityDuration && ysPhysicalActivityDuration && twoLinesFit"
      class="picasso-schedule-entry-ys-physical-activity-duration-container"
      :class="{ 'picasso-schedule-entry-compact': compact }"
      :style="{ fontSize: fontSize, color: textColor }"
    >
      <View
        class="picasso-schedule-entry-spacer"
        :class="{ 'picasso-schedule-entry-spacer-compact': compact }"
      />
      <Text>{{ ysPhysicalActivityDuration }}</Text>
      <View class="picasso-schedule-entry-spacer" />
    </View>
    <View
      class="picasso-schedule-entry-responsibles-container"
      :class="{ 'picasso-schedule-entry-compact': compact }"
      :style="{ fontSize: fontSize, color: textColor }"
    >
      <View
        class="picasso-schedule-entry-spacer"
        :class="{ 'picasso-schedule-entry-spacer-compact': compact }"
      />
      <Responsibles
        v-if="avatars"
        class="picasso-schedule-entry-responsibles"
        :activity="scheduleEntry.activity()"
        avatars
      />
      <View v-else class="picasso-schedule-entry-responsibles-names">
        <Text>{{ responsibleNames }}</Text>
      </View>
      <View class="picasso-schedule-entry-spacer" />
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import Responsibles from '../Responsibles.vue'
import { contrastColor } from '../../../common/helpers/colors.js'
import { ysPhysicalActivityDurationFormatted } from '../../../common/helpers/ysPhysicalActivityDuration.js'
import { activityResponsiblesCommaSeparated } from '../../../common/helpers/activityResponsibles.js'

const MAX_FONT_SIZE = 8

export default {
  name: 'PicassoScheduleEntry',
  components: { Responsibles },
  extends: PdfComponent,
  props: {
    scheduleEntry: { type: Object, required: true },
    percentageHeight: { type: Number, default: 10 },
    avatars: { type: Boolean, default: false },
    config: { type: Object, required: true },
    showYSPhysicalActivityDuration: { type: Boolean, default: false },
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
    ysPhysicalActivityDuration() {
      return ysPhysicalActivityDurationFormatted(
        this.scheduleEntry.ysPhysicalActivityDurationMinutes,
        this.$tc.bind(this)
      )
    },
    ysPhysicalActivityDurationAfterTitle() {
      if (
        !this.showYSPhysicalActivityDuration ||
        !this.ysPhysicalActivityDuration ||
        this.twoLinesFit
      ) {
        return ''
      }
      return ` (${this.ysPhysicalActivityDuration})`
    },
    responsibleNames() {
      return activityResponsiblesCommaSeparated(
        this.scheduleEntry.activity(),
        this.$tc.bind(this)
      )
    },
    linkTarget() {
      return `#scheduleEntry_${this.scheduleEntry.id}`
    },
    fontSizeScalingFactor() {
      switch (this.config.options?.pageSize) {
        case 'A3':
          return 5
        case 'A5':
          return 2.5
        default:
          return 3
      }
    },
    textHeight() {
      return this.fontSizeScalingFactor * this.percentageHeight
    },
    fontSize() {
      return Math.min(MAX_FONT_SIZE, this.textHeight) + 'pt'
    },
    twoLinesFit() {
      return this.textHeight >= 2 * MAX_FONT_SIZE
    },
  },
}
</script>
<style lang="react-pdf">
.picasso-schedule-entry-link {
  text-decoration: none;
  color: black;
}
.picasso-schedule-entry {
  position: absolute;
  padding: 0 4pt;
  flex-direction: column;
  justify-content: flex-start;
}
.picasso-schedule-entry-compact {
  padding: 0 1pt;
}
.picasso-schedule-entry-spacer {
  height: 0;
  max-height: 4pt;
  flex-grow: 1;
}
.picasso-schedule-entry-spacer-compact {
  max-height: 1pt;
}
.picasso-schedule-entry-title {
  height: 16pt;
  line-height: 1;
  flex-grow: 1;
}
.picasso-schedule-entry-ys-physical-activity-duration-container {
  position: absolute;
  top: 0;
  bottom: 0;
  right: 0;
  left: 0;
  justify-content: flex-end;
  padding: 0 4pt;
}
.picasso-schedule-entry-responsibles-names {
  font-style: italic;
  text-align: right;
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
</style>
