<template>
  <v-tooltip v-if="featureComments && isCollaborator" location="bottom">
    <template #activator="{ props }">
      <v-btn
        variant="text"
        icon
        data-testid="comments-toggle"
        :active="commentsState.open"
        :aria-label="label"
        v-bind="props"
        @click="commentsState.open = !commentsState.open"
      >
        <CommentCountIcon :count="commentCount" :filled="commentsState.open" />
      </v-btn>
    </template>
    {{ label }}
  </v-tooltip>
</template>

<script>
import CommentCountIcon from '@/components/comments/CommentCountIcon.vue'
import { commentsState } from '@/components/comments/commentsState.js'
import { scopedComments } from '@/components/comments/scopedComments.js'
import { getEnv } from '@/environment.js'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import { activityFromRoute, campFromRoute } from '@/router.js'

export default {
  name: 'CommentsToggleButton',
  components: { CommentCountIcon },
  mixins: [campRoleMixin],
  data() {
    return { commentsState }
  },
  computed: {
    camp() {
      return campFromRoute(this.$route)
    },
    activity() {
      return activityFromRoute(this.$route)
    },
    comments() {
      return this.api.get().comments({ camp: this.camp._meta.self })
    },
    commentCount() {
      return scopedComments(this.comments, this.activity, this.$vuetify.display.smAndDown)
        .length
    },
    featureComments() {
      return getEnv().FEATURE_COMMENTS ?? false
    },
    label() {
      if (this.commentsState.open) {
        return this.$t('components.comments.commentsToggleButton.hide')
      }
      return this.$t(
        'components.comments.commentsToggleButton.show',
        { count: this.commentCount },
        this.commentCount
      )
    },
  },
}
</script>
