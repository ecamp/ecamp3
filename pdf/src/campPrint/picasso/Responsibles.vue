<template>
  <View class="picasso-schedule-entry-responsibles-avatars">
    <View
      v-for="(activityResponsible, index) in activity.activityResponsibles().items"
      :key="activityResponsible.campCollaboration().id"
      class="picasso-schedule-entry-responsible-avatar"
      :class="{ 'picasso-schedule-entry-responsibles-avatar-overlap': index !== last }"
      :style="{ backgroundColor: colorFor(activityResponsible) }"
    >
      <Text
        class="picasso-schedule-entry-responsible-initials"
        :style="{ color: fontColorFor(activityResponsible) }"
      >
        {{ initialsFor(activityResponsible) }}
      </Text>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import { campCollaborationColor, contrastColor } from '../../../common/helpers/colors.js'
import campCollaborationInitials from '../../../common/helpers/campCollaborationInitials.js'

export default {
  name: 'Responsibles',
  extends: PdfComponent,
  props: {
    activity: { type: Object, required: true },
  },
  computed: {
    last() {
      return this.activity.activityResponsibles().items.length - 1
    },
  },
  methods: {
    colorFor(activityResponsible) {
      return campCollaborationColor(activityResponsible.campCollaboration())
    },
    fontColorFor(activityResponsible) {
      return contrastColor(this.colorFor(activityResponsible))
    },
    initialsFor(activityResponsible) {
      return campCollaborationInitials(activityResponsible.campCollaboration())
    },
  },
}
</script>
<pdf-style>
.picasso-schedule-entry-responsibles-avatars {
  display: flex;
  flex-direction: row;
  align-items: flex-end;
}
.picasso-schedule-entry-responsible-avatar {
  border-radius: 50%;
  width: 12pt;
  height: 12pt;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.picasso-schedule-entry-responsibles-avatar-overlap {
  margin-right: -2pt;
}
.picasso-schedule-entry-responsible-initials {
  font-size: 6pt;
  text-align: center;
  line-height: 1.4;
}
</pdf-style>
