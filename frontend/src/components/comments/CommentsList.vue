<template>
  <v-list class="overflow-y-auto flex-grow-1 ec-comments-list">
    <template v-if="comments._meta.loading">
      <v-list-item>
        <v-skeleton-loader type="list-item-two-line" />
        <v-skeleton-loader type="list-item-two-line" />
      </v-list-item>
    </template>
    <v-list-item v-else-if="!scopedComments.length">
      <p class="text-body-2 opacity-60 mb-0">
        {{ $t('components.comments.commentsList.empty') }}
      </p>
    </v-list-item>
    <v-list-item v-for="comment in scopedComments" :key="comment._meta.self">
      <CommentCard :comment="comment" :show-activity="!activity" />
    </v-list-item>
  </v-list>
  <v-divider />
  <CommentComposer :camp="camp" :activity="activity" @created="comments.$reload()" />
</template>

<script>
import CommentCard from '@/components/comments/CommentCard.vue'
import CommentComposer from '@/components/comments/CommentComposer.vue'

export default {
  name: 'CommentsList',
  components: { CommentCard, CommentComposer },
  props: {
    camp: { type: Object, required: true },
    activity: { type: Object, default: null },
  },
  computed: {
    comments() {
      return this.api.get().comments({ camp: this.camp._meta.self })
    },
    scopedComments() {
      if (!this.activity) {
        return this.comments.items
      }
      return this.comments.items.filter(
        (comment) =>
          typeof comment.activity === 'function' &&
          comment.activity()._meta.self === this.activity._meta.self
      )
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
