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
        class="text-caption text-medium-emphasis d-flex align-center ga-1 mr-auto"
        data-testid="comment-thread-resolved"
      >
        <v-icon icon="mdi-check-circle-outline" size="16" />
        <template v-if="resolverName">
          {{
            $t('components.comments.commentThread.resolvedBy', {
              user: resolverName,
              date: resolvedDate,
            })
          }}
        </template>
        <template v-else>
          {{ $t('components.comments.commentThread.resolvedOn', { date: resolvedDate }) }}
        </template>
      </span>
      <v-spacer v-else />
      <v-btn
        v-if="!resolved && !replying"
        variant="text"
        size="small"
        prepend-icon="mdi-reply"
        data-testid="comment-thread-reply"
        @click="replying = true"
      >
        {{ $t('components.comments.commentThread.reply') }}
      </v-btn>
      <v-btn
        variant="text"
        size="small"
        :prepend-icon="resolved ? 'mdi-restore' : 'mdi-check'"
        :loading="saving"
        data-testid="comment-thread-resolve"
        @click="toggleResolved"
      >
        {{
          resolved
            ? $t('components.comments.commentThread.reopen')
            : $t('components.comments.commentThread.resolve')
        }}
      </v-btn>
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
import userDisplayName from '@/common/helpers/userDisplayName.js'
import { serverErrorToString } from '@/helpers/serverError.js'

export default {
  name: 'CommentThread',
  components: { CommentCard, CommentComposer },
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
    resolvedDate() {
      return this.$date(this.root.resolvedAt).format(
        this.$t('global.datetime.dateTimeLong')
      )
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
.ec-comment-thread__replies {
  margin-left: 24px;
}
</style>
