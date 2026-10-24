<template>
  <div class="flex-grow-1 overflow-y-auto d-flex flex-column ga-4 px-3 ec-comments-list">
    <template v-if="loading">
      <v-skeleton-loader type="list-item-two-line" />
      <v-skeleton-loader type="list-item-two-line" />
    </template>
    <p
      v-else-if="!comments.length && emptyText"
      class="text-body-2 text-medium-emphasis mb-0"
    >
      {{ emptyText }}
    </p>
    <TransitionGroup v-else name="ec-thread">
      <div v-for="root in roots" :key="root._meta.self" class="ec-comments-list__item">
        <CommentThread
          :root="root"
          :camp="camp"
          :enable-all-comments-tab-hint="enableAllCommentsTabHint"
          :replies="repliesTo(root)"
          :show-context="showContext"
          @replied="$emit('replied')"
        />
      </div>
    </TransitionGroup>
    <slot name="after" />
  </div>
</template>

<script>
import CommentThread from '@/components/comments/CommentThread.vue'
import { mapGetters } from 'vuex'
import { isResolved } from '@/components/comments/commentHelpers.js'

export default {
  name: 'CommentsList',
  components: { CommentThread },
  props: {
    comments: { type: Array, required: true },
    camp: { type: Object, default: null },
    allComments: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
    showContext: { type: Boolean, default: false },
    emptyText: { type: String, default: null },
  },
  emits: ['replied'],
  computed: {
    roots() {
      return this.comments.filter((comment) => typeof comment.parent !== 'function')
    },
    ...mapGetters({ authUser: 'getLoggedInUser' }),
    enableAllCommentsTabHint() {
      const hasResolvedCommentByMe = this.allComments.some((comment) => {
        if (!isResolved(comment)) {
          return false
        }
        const resolver =
          typeof comment.resolvedBy === 'function' ? comment.resolvedBy() : null
        return (
          typeof resolver?.user === 'function' &&
          resolver.user()._meta.self === this.authUser?._meta.self
        )
      })
      return !hasResolvedCommentByMe
    },
  },
  methods: {
    repliesTo(root) {
      return this.comments.filter(
        (comment) =>
          typeof comment.parent === 'function' &&
          comment.parent()._meta.self === root._meta.self
      )
    },
    scrollToBottom() {
      const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches
      this.$el.scrollTo({
        top: this.$el.scrollHeight,
        behavior: reduceMotion ? 'auto' : 'smooth',
      })
    },
  },
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

.ec-comments-list__item {
  display: grid;
  grid-template-rows: 1fr;
}

.ec-comments-list__item > * {
  min-height: 0;
}

.ec-thread-enter-active,
.ec-thread-leave-active {
  transition:
    grid-template-rows 0.6s cubic-bezier(0.4, 0, 0.2, 1),
    margin-bottom 0.6s cubic-bezier(0.4, 0, 0.2, 1),
    opacity 0.6s;
}

.ec-thread-enter-active > *,
.ec-thread-leave-active > * {
  overflow: hidden;
}

.ec-thread-enter-from,
.ec-thread-leave-to {
  grid-template-rows: 0fr;
  margin-bottom: -16px;
  opacity: 0;
}

@media (prefers-reduced-motion: reduce) {
  .ec-thread-enter-active,
  .ec-thread-leave-active {
    transition: none;
  }
}
</style>
