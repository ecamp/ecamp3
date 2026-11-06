<template>
  <View class="picasso-schedule-entry-wrapper">
    <View
      class="picasso-schedule-entry"
      :class="[
        { 'picasso-schedule-entry-compact': compact },
        `picasso-schedule-entry-variant-${variant}`,
      ]"
      :style="{ backgroundColor: color }"
    >
      <View
        class="picasso-schedule-entry-spacer"
        :class="{ 'picasso-schedule-entry-spacer-compact': compact }"
      />
      <Link class="picasso-schedule-entry-link" :href="linkTarget">
        <Text
          class="picasso-schedule-entry-title"
          :style="{ fontSize: fontSize + 'pt', color: textColor }"
        >
          <Text :style="{ fontWeight: 500 }"
            ><Text class="tabular-nums">{{ scheduleEntry.number }}</Text>
            {{ category + ': ' + title }}
          </Text>
          <Text :style="{ fontSize: fontSize * 0.8 + 'pt' }">{{
            ysPhysicalActivityDurationAfterTitle
          }}</Text>
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
        :style="{ fontSize: fontSize * 0.8 + 'pt', color: textColor }"
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
        :style="{ fontSize: fontSize * 0.8 + 'pt', color: textColor }"
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
          <Text v-for="name in responsibleNames">{{ name }}</Text>
        </View>
        <View class="picasso-schedule-entry-spacer" />
      </View>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import Responsibles from '../Responsibles.vue'
import { contrastColor } from '../../../helpers/colors.js'
import { ysPhysicalActivityDurationFormatted } from '../../../helpers/ysPhysicalActivityDuration.js'
import { activityResponsibleNames } from '../../../helpers/activityResponsibles.js'

const MAX_FONT_SIZE = 8

export default {
  name: 'PicassoScheduleEntry',
  components: { Responsibles },
  extends: PdfComponent,
  props: {
    scheduleEntry: { type: Object, required: true },
    percentageHeight: { type: Number, default: 10 },
    variant: { type: String, default: 'default' },
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
      return ` (${this.ysPhysicalActivityDuration.replace(/ /g, '\u00a0')})`
    },
    responsibleNames() {
      const names = activityResponsibleNames(
        this.scheduleEntry.activity(),
        this.$tc.bind(this)
      )
      return names
        .map((name, index) => (index < names.length - 1 ? `${name},` : name))
        .reverse()
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
      return Math.min(MAX_FONT_SIZE, this.textHeight)
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
.picasso-schedule-entry-wrapper {
  display: flex;
  position: absolute;
  flex-direction: column;
}
.picasso-schedule-entry {
  margin-bottom: 0.75pt;
  padding: 0 1.5pt;
  flex-grow: 1;
  flex-basis: 0;
  flex-direction: column;
  justify-content: flex-start;
  min-height: 0;
}
.picasso-schedule-entry-variant-start,
.picasso-schedule-entry-variant-default {
  border-top-left-radius: 1.5pt;
  border-top-right-radius: 1.5pt;
}
.picasso-schedule-entry-variant-end,
.picasso-schedule-entry-variant-default {
  border-bottom-left-radius: 1.5pt;
  border-bottom-right-radius: 1.5pt;
}
.picasso-schedule-entry-compact {
  margin-bottom: 0.2pt;
  padding: 0 0.5pt;
}
.picasso-schedule-entry-spacer {
  height: 0;
  max-height: 1pt;
  flex-grow: 1;
}
.picasso-schedule-entry-spacer-compact {
  max-height: 0pt;
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
  padding: 0 1.5pt;
}
.picasso-schedule-entry-responsibles-names {
  display: flex;
  flex-direction: row-reverse;
  flex-wrap: wrap-reverse;
  font-style: italic;
  text-align: right;
  gap: 1pt;
  padding-right: 1pt;
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
  margin-right: 1pt;
}
</style>
