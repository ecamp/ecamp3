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
      :class="{ 'ec-comments-list__group--focused': group.focused }"
    >
      <h3
        v-if="group.activity || group.titleKey"
        tabindex="-1"
        class="text-body-1 font-weight-medium"
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
import { commentsState } from '@/components/comments/commentsState.js'
import { scopedComments } from '@/components/comments/scopedComments.js'
import { sortBy } from 'lodash-es'

export default {
  name: 'CommentsList',
  components: { CommentCard, ScheduleEntryLinks },
  props: {
    camp: { type: Object, required: true },
    activity: { type: Object, default: null },
    comments: { type: Object, required: true },
  },
  data() {
    return { commentsState }
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
      const comments = scopedComments(this.comments, this.activity)
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
            focused: activity._meta.self === this.commentsState.focusedActivity,
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
  watch: {
    'commentsState.focusedActivity': {
      immediate: true,
      handler() {
        this.$nextTick(() => {
          const group = this.$el.querySelector('.ec-comments-list__group--focused')
          if (!group) return
          const reduceMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
          ).matches
          const scrollBehavior = reduceMotion ? 'auto' : 'smooth'
          group.scrollIntoView({ block: 'start', behavior: scrollBehavior })
          group.querySelector('h3')?.focus({ preventScroll: true })
        })
      },
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

.ec-comments-list__group {
  scroll-margin-top: var(--fade);
}

.ec-comments-list__group--focused {
  border-radius: 4px;
  outline: 2px solid transparent;
  outline-offset: 5px;
  animation: ec-comments-list-flash 0.5s ease-out 3;
}

@media (prefers-reduced-motion: reduce) {
  .ec-comments-list__group--focused {
    animation: none;
  }
}

@keyframes ec-comments-list-flash {
  from {
    background-color: rgba(var(--v-theme-primary), 0.16);
    outline-color: rgba(var(--v-theme-primary), 0.7);
  }
  to {
    background-color: transparent;
    outline-color: transparent;
  }
}
</style>
