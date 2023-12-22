<template>
  <View v-if="avatars" class="responsibles-avatars">
    <View
      v-for="(activityResponsible, index) in activity.activityResponsibles().items"
      class="responsibles-avatar"
      :class="{ 'responsibles-avatar-overlap': index !== last }"
      :style="{ backgroundColor: colorFor(activityResponsible) }"
    >
      <Text
        class="responsibles-initials"
        :style="{ color: fontColorFor(activityResponsible) }"
      >
        {{ initialsFor(activityResponsible) }}
      </Text>
    </View>
  </View>
  <View v-else style="display: flex; flex-direction: row; flex-wrap: wrap">
    <Text v-for="(responsible, index) in responsibles"
      >{{ responsible
      }}<template v-if="index + 1 < responsibles.length">, </template></Text
    >
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import { campCollaborationColor, contrastColor } from '../../../common/helpers/colors.js'
import campCollaborationInitials from '../../../common/helpers/campCollaborationInitials.js'
import campCollaborationDisplayName from '../../common/helpers/campCollaborationDisplayName.js'

export default {
  name: 'Responsibles',
  extends: PdfComponent,
  props: {
    activity: { type: Object, required: true },
    avatars: { type: Boolean, default: false },
  },
  computed: {
    last() {
      return this.activity.activityResponsibles().items.length - 1
    },
    responsibles() {
      return this.activity.activityResponsibles().items.map(this.displayNameFor)
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
    displayNameFor(activityResponsible) {
      return campCollaborationDisplayName(
        activityResponsible.campCollaboration(),
        this.$tc.bind(this)
      )
    },
  },
}
</script>
<pdf-style>
.responsibles-avatars {
  display: flex;
  flex-direction: row;
  align-items: flex-end;
}
.responsibles-avatar {
  border-radius: 50%;
  width: 12pt;
  height: 12pt;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.responsibles-avatar-overlap {
  margin-right: -2pt;
}
.responsibles-initials {
  font-size: 6pt;
  text-align: center;
  line-height: 1.4;
}
</pdf-style>
