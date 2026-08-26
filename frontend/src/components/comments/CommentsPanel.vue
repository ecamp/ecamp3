<template>
  <component
    :is="mobile ? 'VDialog' : 'VNavigationDrawer'"
    v-if="isCollaborator"
    :model-value="commentsState.open"
    data-testid="comments-panel"
    v-bind="wrapperProps"
    @update:model-value="commentsState.open = $event"
  >
    <v-sheet class="d-flex flex-column h-100">
      <v-toolbar density="compact" color="transparent">
        <v-toolbar-title tag="h2" class="text-subtitle-1 font-weight-bold">
          <template v-if="!activity">
            {{ $t('components.comments.commentsPanel.title') }}
          </template>
          <template v-else-if="mobile">
            <CategoryChip v-if="category" small dense :category="category" class="mr-1" />
            {{ activity.title || $t('components.comments.commentsPanel.title') }}
          </template>
          <template v-else>
            {{ $t('components.comments.commentsPanel.commentsOnThisActivity') }}
          </template>
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
      <p
        v-if="!activity && !mobile && hiddenCommentCount"
        class="text-caption text-medium-emphasis px-3 pt-3 mb-0"
        data-testid="comments-elsewhere-notice"
      >
        {{
          $t(
            'components.comments.commentsPanel.activityCommentsElsewhere',
            { count: hiddenCommentCount },
            hiddenCommentCount
          )
        }}
      </p>
      <CommentsList
        v-if="commentsState.open"
        ref="list"
        :comments="visibleComments"
        :loading="comments._meta.loading"
        :show-context="showContext"
      />
      <v-divider />
      <CommentComposer
        v-if="commentsState.open"
        :camp="camp"
        :activity="activity"
        @created="reloadAndScrollToBottom"
      />
    </v-sheet>
  </component>
</template>

<script>
import CommentComposer from '@/components/comments/CommentComposer.vue'
import CommentsList from '@/components/comments/CommentsList.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import { VDialog, VNavigationDrawer } from 'vuetify/components'
import {
  clearActivityFilter,
  commentsState,
  resetCommentsState,
} from '@/components/comments/commentsState.js'
import { scopedComments } from '@/components/comments/scopedComments.js'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import { activityFromRoute, campFromRoute } from '@/router.js'

export default {
  name: 'CommentsPanel',
  components: {
    CategoryChip,
    CommentComposer,
    CommentsList,
    VDialog,
    VNavigationDrawer,
  },
  mixins: [campRoleMixin],
  data() {
    return { commentsState }
  },
  computed: {
    camp() {
      return campFromRoute(this.$route)
    },
    activity() {
      return activityFromRoute(this.$route) ?? commentsState.activityFilter
    },
    comments() {
      return this.api.get().comments({ camp: this.camp._meta.self })
    },
    mobile() {
      return this.$vuetify.display.smAndDown
    },
    visibleComments() {
      return scopedComments(this.comments, this.activity, this.mobile)
    },
    showContext() {
      return !this.activity && this.mobile
    },
    hiddenCommentCount() {
      return this.comments.items.length - this.visibleComments.length
    },
    category() {
      return typeof this.activity?.category === 'function'
        ? this.activity.category()
        : null
    },
    wrapperProps() {
      if (this.mobile) {
        return { fullscreen: true, transition: 'dialog-bottom-transition' }
      }
      return {
        location: 'right',
        width: this.width,
        mobile: false,
        order: 2,
        tag: 'aside',
        'aria-label': this.$t('components.comments.commentsPanel.title'),
      }
    },
    width() {
      return this.$vuetify.display.xlAndUp
        ? 480
        : this.$vuetify.display.lgAndUp
          ? 400
          : 320
    },
  },
  watch: {
    'commentsState.open'(open) {
      if (!open) clearActivityFilter()
    },
    '$route.params.campId'() {
      resetCommentsState()
    },
    '$route.path'() {
      if (this.mobile) commentsState.open = false
      clearActivityFilter()
    },
  },
  methods: {
    async reloadAndScrollToBottom() {
      await this.comments.$reload()
      await this.$nextTick()
      this.$refs.list?.scrollToBottom()
    },
  },
}
</script>
