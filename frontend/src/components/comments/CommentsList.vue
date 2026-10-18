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
    <CommentThread
      v-for="root in roots"
      v-else
      :key="root._meta.self"
      :root="root"
      :replies="repliesTo(root)"
      :show-context="showContext"
      @replied="$emit('replied')"
    />
    <slot name="after" />
  </div>
</template>

<script>
import CommentThread from '@/components/comments/CommentThread.vue'

export default {
  name: 'CommentsList',
  components: { CommentThread },
  props: {
    comments: { type: Array, required: true },
    loading: { type: Boolean, default: false },
    showContext: { type: Boolean, default: false },
    emptyText: { type: String, default: null },
  },
  emits: ['replied'],
  computed: {
    roots() {
      return this.comments.filter((comment) => typeof comment.parent !== 'function')
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
</style>
