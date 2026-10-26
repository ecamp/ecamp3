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
      <v-chip-group
        v-model="commentsState.statusFilter"
        mandatory
        class="px-3 pt-0 pb-2"
        data-testid="comments-resolved-toggle"
      >
        <v-chip
          v-for="status in ['open', 'resolved', 'all']"
          :key="status"
          :value="status"
          label
          color="primary"
          base-color="surface"
          :border="commentsState.statusFilter === status ? null : 'sm'"
          :variant="commentsState.statusFilter === status ? 'outlined' : 'flat'"
        >
          {{ $t(`components.comments.commentsPanel.${status}`) }}
          <CountBadge
            v-if="status !== 'all' && statusCounts[status] > 0"
            :count="statusCounts[status]"
            :data-testid="`comments-${status}-count`"
          />
        </v-chip>
      </v-chip-group>
      <v-divider />
      <CommentsList
        v-if="commentsState.open"
        ref="list"
        :status-filter="commentsState.statusFilter"
        :comments="visibleComments"
        :camp="camp"
        :loading="comments._meta.loading"
        :show-context="showContext"
        :empty-text="emptyText"
        @replied="reload"
      >
        <template #after>
          <p
            v-if="!activity && !mobile && hiddenCommentCount"
            class="text-body-2 text-medium-emphasis pl-6"
            data-testid="comments-elsewhere-notice"
          >
            <v-icon icon="mdi-alert-circle-outline" size="20" class="ml-n6" />
            {{
              $t(
                commentsState.statusFilter === 'resolved'
                  ? 'components.comments.commentsPanel.resolvedActivityCommentsElsewhere'
                  : 'components.comments.commentsPanel.activityCommentsElsewhere',
                { count: hiddenCommentCount },
                hiddenCommentCount
              )
            }}
          </p>
        </template>
      </CommentsList>
      <v-divider />
      <CommentComposer
        v-if="commentsState.open && commentsState.statusFilter !== 'resolved'"
        :camp="camp"
        :activity="activity"
        class="pa-3"
        @created="reloadAndScrollToBottom"
      />
    </v-sheet>
  </component>
</template>

<script>
import CommentComposer from '@/components/comments/CommentComposer.vue'
import CommentsList from '@/components/comments/CommentsList.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import CountBadge from '@/components/dashboard/CountBadge.vue'
import { VDialog, VNavigationDrawer } from 'vuetify/components'
import {
  clearActivityFilter,
  commentsState,
  resetCommentsState,
} from '@/components/comments/commentsState.js'
import { scopedComments, isResolved } from '@/components/comments/commentHelpers.js'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import { activityFromRoute, campFromRoute } from '@/router.js'

export default {
  name: 'CommentsPanel',
  components: {
    CountBadge,
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
    statusCounts() {
      const comments = this.visibleComments
      const resolved = comments.filter(isResolved).length
      return { open: comments.length - resolved, resolved }
    },
    visibleComments() {
      return scopedComments(this.comments, this.activity, this.mobile, 'all')
    },
    emptyText() {
      if (
        commentsState.statusFilter === 'all' ||
        this.statusCounts[commentsState.statusFilter]
      )
        return null
      if (commentsState.statusFilter === 'open') {
        return this.$t(
          this.statusCounts.resolved > 0
            ? 'components.comments.commentsList.noOpenCommentsWithResolved'
            : 'components.comments.commentsList.noOpenComments'
        )
      }
      return this.$t(
        this.statusCounts.open
          ? 'components.comments.commentsList.noResolvedComments'
          : 'components.comments.commentsList.empty'
      )
    },
    showContext() {
      return !this.activity && this.mobile
    },
    hiddenCommentCount() {
      return this.comments.items.filter(
        (comment) =>
          typeof comment.activity === 'function' &&
          isResolved(comment) === (commentsState.statusFilter === 'resolved')
      ).length
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
    reload() {
      return this.comments.$reload()
    },
    async reloadAndScrollToBottom() {
      await this.comments.$reload()
      await this.$nextTick()
      this.$refs.list?.scrollToBottom()
    },
  },
}
</script>
