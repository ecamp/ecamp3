<template>
  <v-tooltip v-if="featureComments && isCollaborator" location="bottom">
    <template #activator="{ props }">
      <v-btn
        variant="text"
        icon="mdi-comment-outline"
        data-testid="comments-toggle"
        :active="commentsState.open"
        :aria-label="label"
        v-bind="props"
        @click="commentsState.open = !commentsState.open"
      />
    </template>
    {{ label }}
  </v-tooltip>
</template>

<script>
import { commentsState } from '@/components/comments/commentsState.js'
import { getEnv } from '@/environment.js'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import { campFromRoute } from '@/router.js'

export default {
  name: 'CommentsToggleButton',
  mixins: [campRoleMixin],
  data() {
    return { commentsState }
  },
  computed: {
    camp() {
      return campFromRoute(this.$route)
    },
    featureComments() {
      return getEnv().FEATURE_COMMENTS ?? false
    },
    label() {
      return this.commentsState.open
        ? this.$t('components.comments.commentsToggleButton.hide')
        : this.$t('components.comments.commentsToggleButton.show')
    },
  },
}
</script>
