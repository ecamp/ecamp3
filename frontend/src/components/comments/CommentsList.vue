<!--
Fetches a camp's comments and shows the ones belonging to the panel's current scope.
-->

<template>
  <div class="d-flex flex-column ec-comments-list">
    <div class="flex-grow-1 overflow-y-auto d-flex flex-column gap-2 px-3 py-3">
      <template v-if="comments._meta.loading">
        <v-skeleton-loader type="list-item-two-line" />
        <v-skeleton-loader type="list-item-two-line" />
      </template>
      <p v-else-if="!scopedComments.length" class="text-body-2 opacity-60 mb-0">
        {{ $t('components.comments.commentsList.empty') }}
      </p>
      <CommentCard
        v-for="comment in scopedComments"
        :key="comment._meta.self"
        :comment="comment"
        :show-activity="!activity"
      />
    </div>
    <v-divider />
    <CommentComposer :camp="camp" :activity="activity" @created="comments.$reload()" />
  </div>
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
  min-height: 0;
}
</style>
