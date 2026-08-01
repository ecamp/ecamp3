<template>
  <v-sheet
    border
    rounded="lg"
    class="ec-comment-card px-3 py-2"
    data-testid="comment-card"
  >
    <div
      v-if="showActivity && (activity || comment.orphanDescription)"
      class="ec-comment-card__activity text-body-3 font-weight-medium truncate mb-1"
    >
      <ScheduleEntryLinks v-if="activity" :activity-promise="activity._meta.load" />
      <template v-else>
        {{
          $t('components.comments.commentCard.deletedActivity', {
            activity: comment.orphanDescription,
          })
        }}
      </template>
    </div>
    <div class="ec-comment-card__meta d-flex items-center">
      <UserAvatar :user="author" size="24" class="flex-none mr-2" />
      <span class="text-body-2 truncate">{{ author.displayName }}</span>
      <v-spacer />
      <span class="text-caption opacity-60 flex-none ml-2">
        {{ $date(comment.createTime).format($t('global.datetime.dateTimeLong')) }}
      </span>
      <PromptEntityDelete v-if="isOwnComment" :entity="comment._meta.self">
        <template #activator="{ props }">
          <v-btn
            icon="mdi-delete"
            variant="text"
            size="x-small"
            class="ec-comment-card__delete visible-on-hover flex-none"
            :aria-label="$t('global.button.delete')"
            v-bind="props"
          />
        </template>
        {{ $t('components.comments.commentCard.deleteWarning') }}
      </PromptEntityDelete>
    </div>
    <TiptapEditor
      :model-value="comment.textHtml"
      :with-extensions="true"
      :editable="false"
    />
  </v-sheet>
</template>

<script>
import PromptEntityDelete from '@/components/prompt/PromptEntityDelete.vue'
import ScheduleEntryLinks from '@/components/material/ScheduleEntryLinks.vue'
import TiptapEditor from '@/components/form/tiptap/TiptapEditor.vue'
import UserAvatar from '@/components/user/UserAvatar.vue'
import { mapGetters } from 'vuex'

export default {
  name: 'CommentCard',
  components: { PromptEntityDelete, ScheduleEntryLinks, TiptapEditor, UserAvatar },
  props: {
    comment: { type: Object, required: true },
    showActivity: { type: Boolean, default: false },
  },
  computed: {
    ...mapGetters({ authUser: 'getLoggedInUser' }),
    author() {
      return this.comment.author()
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
