<template>
  <v-sheet tag="article" rounded="lg" color="surface-light">
    <div
      v-if="comment.orphanDescription || showContext"
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
        <UserAvatar :user="author" size="24" class="flex-0-0 mr-2" />
        <strong class="text-body-2 font-weight-medium text-truncate">{{
          author.displayName
        }}</strong>
        <v-spacer />
        <span class="text-caption text-medium-emphasis flex-0-0 mx-2">
          {{ $date(comment.createTime).format($t('global.datetime.dateTimeLong')) }}
        </span>
        <PromptEntityDelete v-if="isOwnComment" :entity="comment._meta.self">
          <template #activator="{ props }">
            <v-btn
              icon
              variant="text"
              size="x-small"
              class="ec-comment-card__delete visible-on-hover flex-0-0"
              :aria-label="$t('global.button.delete')"
              v-bind="props"
            >
              <v-icon size="small">mdi-delete</v-icon>
            </v-btn>
          </template>
          {{ $t('components.comments.commentCard.deleteWarning') }}
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
import PromptEntityDelete from '@/components/prompt/PromptEntityDelete.vue'
import ScheduleEntryLinks from '@/components/material/ScheduleEntryLinks.vue'
import TiptapEditor from '@/components/form/tiptap/TiptapEditor.vue'
import UserAvatar from '@/components/user/UserAvatar.vue'
import { mapGetters } from 'vuex'
import { userColor, lighten } from '@/common/helpers/colors'

export default {
  name: 'CommentCard',
  components: { PromptEntityDelete, ScheduleEntryLinks, TiptapEditor, UserAvatar },
  props: {
    comment: { type: Object, required: true },
    showContext: { type: Boolean, default: false },
  },
  computed: {
    ...mapGetters({ authUser: 'getLoggedInUser' }),
    author() {
      return this.comment.author()
    },
    bgColor() {
      return lighten(userColor(this.author))
    },
    activity() {
      return typeof this.comment.activity === 'function' ? this.comment.activity() : null
    },
    isOwnComment() {
      return this.author._meta.self === this.authUser?._meta.self
    },
  },
}
</script>

<style scoped>
.ec-comment-card__meta {
  min-height: 32px;
}

.ec-comment-card:not(:hover) :deep(button.visible-on-hover:not(:focus)) {
  opacity: 0;
  max-width: 0;
  overflow: hidden;
}

.ec-comment-card :deep(button.visible-on-hover) {
  opacity: 1;
  max-width: 2rem;
  transition:
    opacity 0.1s linear,
    max-width 0.1s linear;
}
</style>
