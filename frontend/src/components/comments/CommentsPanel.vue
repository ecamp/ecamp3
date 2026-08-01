<!--
A global sidebar panel (bottom on mobile) showing comments for the current context, depending on the route.
-->

<template>
  <v-navigation-drawer
    v-if="isCollaborator"
    v-model="commentsState.open"
    :location="atBottom ? 'bottom' : 'right'"
    :width="width"
    :mobile="false"
    :order="2"
    :class="atBottom ? 'ec-comments-panel__mobile' : ''"
    data-testid="comments-panel"
  >
    <div class="d-flex flex-column h-100">
      <v-toolbar density="compact" color="transparent">
        <v-toolbar-title class="text-subtitle-1 font-weight-bold">
          {{ $t('components.comments.commentsPanel.title') }}
        </v-toolbar-title>
        <v-btn
          icon="mdi-close"
          variant="text"
          density="comfortable"
          :aria-label="$t('global.button.close')"
          @click="commentsState.open = false"
        />
      </v-toolbar>
      <v-divider />
      <CommentsList
        v-if="commentsState.open"
        class="flex-grow-1"
        :camp="camp"
        :activity="activity"
      />
    </div>
  </v-navigation-drawer>
</template>

<script>
import CommentsList from '@/components/comments/CommentsList.vue'
import { commentsState, resetCommentsState } from '@/components/comments/commentsState.js'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import { activityFromRoute, campFromRoute } from '@/router.js'

export default {
  name: 'CommentsPanel',
  components: { CommentsList },
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
    atBottom() {
      return this.$vuetify.display.smAndDown
    },
    width() {
      if (this.atBottom) {
        return Math.round(this.$vuetify.display.height * 0.6)
      }
      return this.$vuetify.display.xlAndUp
        ? 480
        : this.$vuetify.display.lgAndUp
          ? 400
          : 320
    },
  },
  watch: {
    '$route.params.campId'() {
      resetCommentsState()
    },
  },
}
</script>

<style scoped>
.ec-comments-panel__mobile {
  border-top: 1px solid rgba(0, 0, 0, 0.6);
}
</style>
