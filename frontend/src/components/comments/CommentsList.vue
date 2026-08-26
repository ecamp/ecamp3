<template>
  <div class="flex-grow-1 overflow-y-auto d-flex flex-column ga-4 px-3 ec-comments-list">
    <template v-if="loading">
      <v-skeleton-loader type="list-item-two-line" />
      <v-skeleton-loader type="list-item-two-line" />
    </template>
    <p v-else-if="!comments.length" class="text-body-2 text-medium-emphasis mb-0">
      {{ $t('components.comments.commentsList.empty') }}
    </p>
    <CommentCard
      v-for="comment in comments"
      v-else
      :key="comment._meta.self"
      :comment="comment"
      :show-context="showContext"
    />
    <slot name="after" />
  </div>
</template>

<script>
import CommentCard from '@/components/comments/CommentCard.vue'

export default {
  name: 'CommentsList',
  components: { CommentCard },
  props: {
    comments: { type: Array, required: true },
    loading: { type: Boolean, default: false },
    showContext: { type: Boolean, default: false },
  },
  methods: {
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
