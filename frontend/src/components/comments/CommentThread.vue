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
      <CommentCard v-for="reply in replies" :key="reply._meta.self" :comment="reply" />
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
import { useToast } from '@/components/toast/useToast.js'
import { commentsState } from '@/components/comments/commentsState.js'
import { errorToMultiLineToast } from '@/components/toast/toasts.js'

export default {
  name: 'CommentThread',
  components: { CommentCard, CommentComposer, CommentTime },
  props: {
    root: { type: Object, required: true },
    replies: { type: Array, default: () => [] },
    showContext: { type: Boolean, default: false },
  },
  emits: ['replied'],
  setup() {
    return { toast: useToast() }
  },
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
      try {
        const resolving = !this.resolved
        await this.api.patch(this.root, { resolved: resolving })
        this.replying = false
        if (resolving) this.offerUndo()
      } catch (e) {
        this.errorMessage = serverErrorToString(e)
      } finally {
        this.saving = false
      }
    },
    offerUndo() {
      const { api, root, toast } = this
      const actions = [
        {
          label: this.$t('global.button.undo'),
          onClick: () =>
            api
              .patch(root, { resolved: false })
              .catch((e) => toast.error(errorToMultiLineToast(e))),
        },
      ]
      if (!commentsState.showResolved) {
        actions.push({
          label: this.$t('components.comments.commentThread.showAll'),
          onClick: () => (commentsState.showResolved = true),
        })
      }
      toast.info(this.$t('components.comments.commentThread.resolvedToast'), { actions })
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
