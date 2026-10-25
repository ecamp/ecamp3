<template>
  <v-sheet tag="article" rounded="lg" color="surface-light" class="ec-comment">
    <div
      v-if="(comment.orphanDescription || showContext) && !isReply"
      class="ec-comment-card__activity text-body-2 mx-2 my-1"
      :class="{ 'font-weight-medium': showContext }"
    >
      <template v-if="comment.orphanDescription">
        <v-icon icon="mdi-ghost" size="16" class="opacity-30" />
        <strong class="font-weight-medium mx-1 flex-shrink-1">{{
          comment.orphanDescription
        }}</strong>
        <i class="text-medium-emphasis">{{
          $t('components.comments.commentCard.deleted')
        }}</i>
      </template>
      <template v-else-if="showContext">
        <ScheduleEntryLinks v-if="activity" :activity-promise="activity._meta.load" />
        <template v-else>{{
          $t('components.comments.commentCard.campComment')
        }}</template>
      </template>
    </div>
    <v-sheet
      border
      rounded="lg"
      :color="bgColor"
      class="ec-comment-card px-3 py-2"
      data-testid="comment-card"
    >
      <header class="ec-comment-card__meta d-flex align-center">
        <UserAvatar v-if="author" :camp-collaboration="author" size="24" class="mr-2" />
        <v-avatar v-else size="24" color="grey-lighten-2" class="mr-2">
          <v-icon icon="mdi-account-off-outline" size="16" />
        </v-avatar>
        <strong class="text-body-2 font-weight-medium text-truncate">{{
          authorName
        }}</strong>
        <v-spacer />
        <CommentTime
          :value="comment.createTime"
          class="text-caption text-medium-emphasis mx-2"
        />
        <v-tooltip v-if="isOwnComment && hasRepliesByOthers" location="bottom">
          <template #activator="{ props }">
            <span class="visible-on-hover d-inline-flex" tabindex="0" v-bind="props">
              <v-btn
                icon
                variant="text"
                size="x-small"
                class="ec-comment-card__delete"
                disabled
                :aria-label="$t('global.button.delete')"
              >
                <v-icon size="small">mdi-delete</v-icon>
              </v-btn>
            </span>
          </template>
          {{ $t('components.comments.commentCard.deleteDisabledReplies') }}
        </v-tooltip>
        <PromptEntityDelete v-else-if="isOwnComment" :entity="comment._meta.self">
          <template #activator="{ props }">
            <v-btn
              icon
              variant="text"
              size="x-small"
              class="ec-comment-card__delete visible-on-hover"
              :aria-label="$t('global.button.delete')"
              v-bind="props"
            >
              <v-icon size="small">mdi-delete</v-icon>
            </v-btn>
          </template>
          {{
            hasReplies
              ? $t('components.comments.commentCard.deleteWithRepliesWarning')
              : $t('components.comments.commentCard.deleteWarning')
          }}
        </PromptEntityDelete>
      </header>
      <TiptapEditor
        :model-value="comment.textHtml"
        :with-extensions="true"
        :editable="false"
      />
    </v-sheet>
  </v-sheet>
</template>

<script>
import CommentTime from '@/components/comments/CommentTime.vue'
import PromptEntityDelete from '@/components/prompt/PromptEntityDelete.vue'
import ScheduleEntryLinks from '@/components/material/ScheduleEntryLinks.vue'
import TiptapEditor from '@/components/form/tiptap/TiptapEditor.vue'
import UserAvatar from '@/components/user/UserAvatar.vue'
import { mapGetters } from 'vuex'
import { campCollaborationColor, lighten } from '@/common/helpers/colors'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'

export default {
  name: 'CommentCard',
  components: {
    CommentTime,
    PromptEntityDelete,
    ScheduleEntryLinks,
    TiptapEditor,
    UserAvatar,
  },
  props: {
    comment: { type: Object, required: true },
    showContext: { type: Boolean, default: false },
    hasReplies: { type: Boolean, default: false },
    hasRepliesByOthers: { type: Boolean, default: false },
    isReply: { type: Boolean, default: false },
  },
  computed: {
    ...mapGetters({ authUser: 'getLoggedInUser' }),
    author() {
      return typeof this.comment.author === 'function' ? this.comment.author() : null
    },
    authorName() {
      return this.author
        ? campCollaborationDisplayName(this.author, this.$t.bind(this))
        : (this.comment.authorDisplayName ??
            this.$t('components.comments.commentCard.deletedAuthor'))
    },
    bgColor() {
      return this.author ? lighten(campCollaborationColor(this.author)) : undefined
    },
    activity() {
      return typeof this.comment.activity === 'function' ? this.comment.activity() : null
    },
    isOwnComment() {
      return (
        typeof this.author?.user === 'function' &&
        this.author.user()._meta.self === this.authUser?._meta.self
      )
    },
  },
}
</script>

<style scoped>
.ec-comment {
  display: flow-root;
}

.ec-comment-card__meta {
  min-height: 32px;
}

.ec-comment-card:not(:hover) :deep(.visible-on-hover:not(:focus)) {
  opacity: 0;
  max-width: 0;
  overflow: hidden;
}

.ec-comment-card :deep(.visible-on-hover) {
  opacity: 1;
  max-width: 2rem;
  transition:
    opacity 0.1s linear,
    max-width 0.1s linear;
}
</style>
