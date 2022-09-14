<!--
Displays responsibles of a scheduleEntry as avatars
-->
<template>
  <div class="ec-schedule-entry-responsibles">
    <user-avatar
      v-for="ar in sortActivityResponsibles(
        scheduleEntry.activity().activityResponsibles().items
      )"
      :key="ar._meta.self"
      :camp-collaboration="ar.campCollaboration()"
      :size="avatarSize"
    />
  </div>
</template>

<script>
import UserAvatar from '@/components/user/UserAvatar.vue'
import { sortBy } from 'lodash'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'

export default {
  name: 'ScheduleEntryResponsibles',
  components: {
    UserAvatar,
  },
  props: {
    scheduleEntry: {
      type: Object,
      required: true,
    },
    avatarSize: {
      type: Number,
      required: false,
      default: 24,
    },
  },
  methods: {
    sortActivityResponsibles(activityResponsibles) {
      return sortBy(activityResponsibles, (activityResponsible) =>
        campCollaborationDisplayName(activityResponsible.campCollaboration(), null, false)
      )
    },
  },
}
</script>

<style lang="scss">
.ec-schedule-entry-responsibles {
  display: inline-flex;
  gap: 2px;
  margin-left: auto;
  margin-top: auto;

  @media #{map-get($display-breakpoints, 'xs-only')} {
    padding: 1px;
  }
}
</style>
