<template>
  <section class="ec-comment-thread" data-testid="comment-thread">
    <CommentCard
      :comment="root"
      :show-context="showContext"
      :has-replies="!!replies.length"
      :has-replies-by-others="hasRepliesByOthers"
    />
    <div
      v-if="replies.length"
      class="ec-comment-thread__replies d-flex flex-column ga-2 mt-2"
    >
      <CommentCard
        v-for="reply in replies"
        :key="reply._meta.self"
        :comment="reply"
        :is-reply="true"
      />
    </div>
    <div class="ec-comment-thread__actions d-flex align-start mt-1">
      <v-tooltip location="bottom" :text="resolveLabel">
        <template #activator="{ props }">
          <v-btn
            :icon="!resolved"
            variant="text"
            class="ec-comment-thread__resolve"
            :class="resolved ? 'pl-1' : 'ec-comment-thread__resolve--unresolved'"
            :size="resolved ? 'small' : 'no-size'"
            :loading="saving && !resolved"
            :disabled="saving"
            :aria-label="resolveLabel"
            data-testid="comment-thread-resolve"
            v-bind="props"
            @click="toggleResolved"
          >
            <span
              v-if="resolved"
              class="ec-comment-thread__resolved text-caption text-medium-emphasis d-flex align-center ga-1"
              data-testid="comment-thread-resolved"
            >
              <v-progress-circular v-if="saving" indeterminate size="16" width="2" />
              <template v-else>
                <v-icon
                  class="ec-comment-thread__resolved-icon"
                  icon="mdi-check-circle-outline"
                  size="16"
                />
                <v-icon
                  class="ec-comment-thread__reopen-icon"
                  icon="mdi-rotate-left"
                  size="18"
                />
              </template>
              <svg
                v-if="removalCountdown"
                class="ec-comment-thread__countdown"
                viewBox="0 0 16 16"
                width="16"
                height="16"
                aria-hidden="true"
                data-testid="comment-thread-countdown"
                :style="removalCountdown"
              >
                <circle cx="8" cy="8" r="6" pathLength="1" />
              </svg>
              {{
                resolverName
                  ? $t('components.comments.commentThread.resolvedBy', {
                      user: resolverName,
                    })
                  : $t('components.comments.commentThread.resolved')
              }}
              ·
              <CommentTime :value="root.resolvedAt" />
            </span>
            <v-icon v-else class="ec-comment-thread__check-icon" icon="mdi-check" />
          </v-btn>
        </template>
      </v-tooltip>
      <v-btn
        v-if="!resolved && !replying"
        prepend-icon="mdi-arrow-right-bottom"
        variant="text"
        size="small"
        density="default"
        data-testid="comment-thread-reply"
        @click="replying = true"
      >
        {{ $t('components.comments.commentThread.reply') }}
      </v-btn>
      <CommentComposer
        v-if="replying"
        :parent="root"
        cancelable
        autofocus
        class="flex-grow-1 mt-1"
        @created="onReplied"
        @cancel="replying = false"
      />
    </div>
    <p v-if="errorMessage" class="text-caption text-error mb-0">{{ errorMessage }}</p>
  </section>
</template>

<script>
import CommentCard from '@/components/comments/CommentCard.vue'
import CommentComposer from '@/components/comments/CommentComposer.vue'
import CommentTime from '@/components/comments/CommentTime.vue'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'
import { serverErrorToString } from '@/helpers/serverError.js'
import {
  commentsState,
  RESOLVED_COMMENT_GRACE_PERIOD,
  retainCommentThread,
  releaseCommentThread,
  scheduleCommentThreadRemoval,
} from '@/components/comments/commentsState.js'

export default {
  name: 'CommentThread',
  components: { CommentCard, CommentComposer, CommentTime },
  props: {
    root: { type: Object, required: true },
    camp: { type: Object, default: null },
    replies: { type: Array, default: () => [] },
    showContext: { type: Boolean, default: false },
  },
  emits: ['replied', 'reopening'],
  data() {
    return {
      replying: false,
      saving: false,
      errorMessage: null,
    }
  },
  computed: {
    hasRepliesByOthers() {
      const rootAuthor = this.authorOf(this.root)
      return this.replies.some(
        (reply) => !rootAuthor || this.authorOf(reply) !== rootAuthor
      )
    },
    removalCountdown() {
      const deadline = commentsState.removalDeadlines.get(this.root._meta.self)
      if (!this.resolved || commentsState.statusFilter !== 'open' || !deadline)
        return null
      return {
        animationDuration: `${RESOLVED_COMMENT_GRACE_PERIOD}ms`,
        animationDelay: `${Math.min(0, deadline - Date.now() - RESOLVED_COMMENT_GRACE_PERIOD)}ms`,
      }
    },
    resolved() {
      return !!this.root.resolvedAt
    },
    resolverName() {
      return typeof this.root.resolvedBy === 'function'
        ? campCollaborationDisplayName(this.root.resolvedBy(), this.$t.bind(this))
        : null
    },
    resolveLabel() {
      return this.resolved
        ? this.$t('components.comments.commentThread.reopen')
        : this.$t('components.comments.commentThread.resolve')
    },
  },
  methods: {
    authorOf(comment) {
      return typeof comment.author === 'function' ? comment.author()._meta.self : null
    },
    async toggleResolved() {
      if (this.saving) return
      this.saving = true
      this.errorMessage = null
      const resolving = !this.resolved
      const uri = this.root._meta.self
      const retain =
        (resolving && commentsState.statusFilter === 'open') ||
        commentsState.retainedThreads.has(uri)
      if (retain) retainCommentThread(uri)
      if (!resolving) this.$emit('reopening', true)
      try {
        await this.api.patch(this.root, { resolved: resolving })
        this.replying = false
        if (resolving) {
          scheduleCommentThreadRemoval(uri)
        } else {
          releaseCommentThread(uri)
        }
      } catch (e) {
        if (resolving) {
          releaseCommentThread(uri)
        } else {
          this.$emit('reopening', false)
          scheduleCommentThreadRemoval(uri)
        }
        this.errorMessage = serverErrorToString(e)
      } finally {
        this.saving = false
      }
    },
    onReplied() {
      this.replying = false
      this.$emit('replied')
    },
  },
}
</script>

<style scoped>
.ec-comment-thread {
  --ec-guide-offset: 12px;
  --ec-guide-width: 2px;
}

.ec-comment-thread__replies {
  margin-left: var(--ec-guide-offset);
  padding-left: 16px;
  border-left: var(--ec-guide-width) solid
    rgba(var(--v-border-color), var(--v-border-opacity));
}

.ec-comment-thread__actions {
  gap: 6px;
}

.ec-comment-thread__check-icon {
  padding: 2px;
}

.ec-comment-thread__countdown {
  flex-shrink: 0;
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
  transform: rotate(-90deg);
}

.ec-comment-thread__countdown circle {
  stroke-dasharray: 1;
  animation: ec-comment-countdown linear forwards;
  animation-duration: inherit;
  animation-delay: inherit;
}

@keyframes ec-comment-countdown {
  from {
    stroke-dashoffset: 0;
  }
  to {
    stroke-dashoffset: 1;
  }
}

.ec-comment-thread__resolved {
  margin-left: 1px;
}

.ec-comment-thread__resolve--unresolved {
  margin-top: 2px;
  margin-left: 1px;
  margin-right: -1px;
}

.ec-comment-thread__reopen-icon,
.ec-comment-thread__resolve:hover .ec-comment-thread__resolved-icon {
  display: none;
}

.ec-comment-thread__resolve:hover .ec-comment-thread__reopen-icon {
  display: inline-flex;
  margin: -1px;
}
</style>
