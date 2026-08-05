<template>
  <div class="flex-grow-1 overflow-y-auto d-flex flex-column ga-4 px-3 ec-comments-list">
    <template v-if="loading">
      <v-skeleton-loader type="list-item-two-line" />
      <v-skeleton-loader type="list-item-two-line" />
    </template>
    <p v-else-if="!groups.length" class="text-body-2 text-medium-emphasis mb-0">
      {{ $t('components.comments.commentsList.empty') }}
    </p>
    <div
      v-for="group in groups"
      v-else
      :key="group.key"
      class="ec-comments-list__group d-flex flex-column ga-2"
    >
      <h3
        v-if="group.activity || group.titleKey"
        class="text-body-1 font-weight-medium text-truncate"
      >
        <ScheduleEntryLinks
          v-if="group.activity"
          :activity-promise="group.activity._meta.load"
        />
        <template v-else>{{ $t(group.titleKey) }}</template>
      </h3>
      <CommentCard
        v-for="comment in group.comments"
        :key="comment._meta.self"
        :comment="comment"
        :show-activity-title="!!group.showActivityTitle"
      />
    </div>
  </div>
</template>

<script>
import CommentCard from '@/components/comments/CommentCard.vue'
import ScheduleEntryLinks from '@/components/material/ScheduleEntryLinks.vue'
import { firstAppearanceByActivity } from '@/helpers/firstAppearanceByActivity.js'
import { sortBy } from 'lodash-es'

export default {
  name: 'CommentsList',
  components: { CommentCard, ScheduleEntryLinks },
  props: {
    camp: { type: Object, required: true },
    activity: { type: Object, default: null },
    comments: { type: Object, required: true },
  },
  computed: {
    periods() {
      return this.camp.periods().items
    },
    scheduleEntries() {
      return this.periods.flatMap((period) => period.scheduleEntries().items)
    },
    programLoading() {
      return (
        this.camp.periods()._meta.loading ||
        this.periods.some((period) => period.scheduleEntries()._meta.loading)
      )
    },
    loading() {
      return this.comments._meta.loading || (!this.activity && this.programLoading)
    },
    activityScopedGroups() {
      const comments = this.comments.items.filter(
        (comment) =>
          typeof comment.activity === 'function' &&
          comment.activity()._meta.self === this.activity._meta.self
      )
      return comments.length ? [{ key: this.activity._meta.self, comments }] : []
    },
    campScopedGroups() {
      const firstAppearance = firstAppearanceByActivity(this.scheduleEntries)
      const activityGroups = new Map()
      const campComments = []
      const orphanedComments = []

      this.comments.items.forEach((comment) => {
        if (typeof comment.activity !== 'function') {
          if (comment.orphanDescription) orphanedComments.push(comment)
          else campComments.push(comment)
          return
        }
        const activity = comment.activity()
        const position = firstAppearance.get(activity._meta.self)
        if (position === undefined) return
        if (!activityGroups.has(activity._meta.self)) {
          activityGroups.set(activity._meta.self, {
            key: activity._meta.self,
            position,
            activity,
            comments: [],
          })
        }
        activityGroups.get(activity._meta.self).comments.push(comment)
      })

      return [
        ...bucket('camp', 'components.comments.commentsList.campComments', campComments),
        ...sortBy([...activityGroups.values()], 'position'),
        ...bucket(
          'orphaned',
          'components.comments.commentsList.orphanedComments',
          orphanedComments,
          true
        ),
      ]
    },
    groups() {
      return this.activity ? this.activityScopedGroups : this.campScopedGroups
    },
  },
}

function bucket(key, titleKey, comments, showActivityTitle = false) {
  if (!comments.length) return []
  return [{ key, titleKey, comments, showActivityTitle }]
}
</script>

<style scoped>
.ec-comments-list {
  --fade: 24px;
  --fade-padding: 17px;

  padding-top: var(--fade-padding);
  padding-bottom: var(--fade-padding);
  mask-image: linear-gradient(
    to bottom,
    transparent 0,
    #000 var(--fade),
    #000 calc(100% - var(--fade)),
    transparent 100%
  );
}
</style>
