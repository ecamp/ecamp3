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
    <div class="ec-comment-thread__actions d-flex align-center flex-wrap ga-1 mt-1">
      <span
        v-if="resolved"
        class="ec-comment-thread__resolved text-caption text-medium-emphasis d-flex align-center ga-1 mr-auto"
        data-testid="comment-thread-resolved"
      >
        <v-icon icon="mdi-check-circle-outline" size="16" />
        {{
          resolverName
            ? $t('components.comments.commentThread.resolvedBy', { user: resolverName })
            : $t('components.comments.commentThread.resolved')
        }}
        ·
        <CommentTime :value="root.resolvedAt" />
      </span>
      <v-spacer v-else />
      <v-tooltip
        v-if="!resolved && !replying"
        location="bottom"
        :text="$t('components.comments.commentThread.reply')"
      >
        <template #activator="{ props }">
          <v-btn
            icon="mdi-reply"
            variant="text"
            size="small"
            density="comfortable"
            :aria-label="$t('components.comments.commentThread.reply')"
            data-testid="comment-thread-reply"
            v-bind="props"
            @click="replying = true"
          />
        </template>
      </v-tooltip>
      <v-tooltip location="bottom" :text="resolveLabel">
        <template #activator="{ props }">
          <v-btn
            :icon="resolved ? 'mdi-restore' : 'mdi-check'"
            variant="text"
            size="small"
            density="comfortable"
            :loading="saving"
            :aria-label="resolveLabel"
            data-testid="comment-thread-resolve"
            v-bind="props"
            @click="toggleResolved"
          />
        </template>
      </v-tooltip>
    </div>
    <p v-if="errorMessage" class="text-caption text-error mb-0">{{ errorMessage }}</p>
    <CommentComposer
      v-if="replying"
      :parent="root"
      cancelable
      autofocus
      @created="onReplied"
      @cancel="replying = false"
    />
  </section>
</template>

<script>
import CommentCard from '@/components/comments/CommentCard.vue'
import CommentComposer from '@/components/comments/CommentComposer.vue'
import CommentTime from '@/components/comments/CommentTime.vue'
import userDisplayName from '@/common/helpers/userDisplayName.js'
import { serverErrorToString } from '@/helpers/serverError.js'

export default {
  name: 'CommentThread',
  components: { CommentCard, CommentComposer, CommentTime },
  props: {
    root: { type: Object, required: true },
    replies: { type: Array, default: () => [] },
    showContext: { type: Boolean, default: false },
  },
  emits: ['replied'],
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
        ? userDisplayName(this.root.resolvedBy())
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
        await this.api.patch(this.root, { resolved: !this.resolved })
        this.replying = false
      } catch (e) {
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
  padding-left: 10px;
  border-left: var(--ec-guide-width) solid
    rgba(var(--v-border-color), var(--v-border-opacity));
}

.ec-comment-thread__resolved .v-icon {
  margin-left: calc(var(--ec-guide-offset) + var(--ec-guide-width) / 2 - 8px);
}
</style>
