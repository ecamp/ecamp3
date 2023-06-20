<template>
  <View>
    <Text class="picasso-day-header-text">{{ date }}</Text>
    <View v-if="showDayResponsibles" class="picasso-day-responsibles">
      <Text>{{ dayResponsibles }}</Text>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'

export default {
  name: 'DayHeader',
  extends: PdfComponent,
  props: {
    day: { type: Object, required: true },
    showDayResponsibles: { type: Boolean, default: false },
  },
  computed: {
    date() {
      return this.$date.utc(this.day.start).hour(0).minute(0).second(0).format('ddd LL')
    },
    dayResponsibles() {
      const responsibles = this.day.dayResponsibles().items
      if (responsibles.length === 0) return ''
      const label = this.$tc('entity.day.fields.dayResponsibles')
      const displayNames = responsibles
        .map((responsible) =>
          campCollaborationDisplayName(responsible.campCollaboration(), this.$tc)
        )
        .join(', ')
      return `${label}: ${displayNames}`
    },
  },
}
</script>
<pdf-style>
.picasso-day-header-text {
  font-size: 8pt;
  font-weight: bold;
  margin: 0 auto 2pt;
}
.picasso-day-responsibles {
  font-size: 8pt;
  margin: 3pt auto 0;
}
</pdf-style>
