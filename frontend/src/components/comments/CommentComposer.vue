<template>
  <div
    class="ec-comment-composer"
    :class="{ 'ec-comment-composer--reply': parent }"
    data-testid="comment-composer"
    @keydown.capture.ctrl.enter.prevent.stop="submit"
    @keydown.capture.meta.enter.prevent.stop="submit"
    @keydown.capture.esc="cancel"
  >
    <ERichtext
      v-model="textHtml"
      vee-id="textHtml"
      :placeholder="
        parent
          ? $t('components.comments.commentComposer.replyPlaceholder')
          : $t('components.comments.commentComposer.placeholder')
      "
      :error-messages="errorMessages"
      :autofocus="autofocus ? 'end' : false"
      :disabled="saving"
      variant="outlined"
    >
      <template #append-inner>
        <v-btn
          variant="flat"
          density="comfortable"
          color="primary"
          size="small"
          class="ec-comment-composer__action align-self-end"
          :disabled="!textHtml"
          :loading="saving"
          :aria-label="$t('global.button.submit')"
          data-testid="comment-submit"
          @click="submit"
        >
          <v-icon icon="mdi-send-variant" />
        </v-btn>
        <v-btn
          v-if="cancelable"
          variant="flat"
          density="comfortable"
          color="grey"
          class="ec-comment-composer__action align-self-end"
          :disabled="saving"
          :aria-label="$t('global.button.cancel')"
          data-testid="comment-cancel"
          @click="cancel"
        >
          <v-icon icon="mdi-close" />
        </v-btn>
      </template>
    </ERichtext>
  </div>
</template>

<script>
import ERichtext from '@/components/form/base/ERichtext.vue'
import { serverErrorToString } from '@/helpers/serverError.js'

export default {
  name: 'CommentComposer',
  components: { ERichtext },
  props: {
    camp: { type: Object, default: null },
    activity: { type: Object, default: null },
    parent: { type: Object, default: null },
    cancelable: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
  },
  emits: ['created', 'cancel'],
  data() {
    return {
      textHtml: '',
      saving: false,
      errorMessages: [],
    }
  },
  methods: {
    cancel(event) {
      if (!this.cancelable || this.saving) return
      // keep Escape from also closing the fullscreen panel on mobile
      event?.stopPropagation()
      this.$emit('cancel')
    },
    async submit() {
      if (!this.textHtml || this.saving) return
      this.saving = true
      this.errorMessages = []
      try {
        await this.api.post(
          await this.api.href(this.api.get(), 'comments'),
          this.parent
            ? { textHtml: this.textHtml, parent: this.parent._meta.self }
            : {
                textHtml: this.textHtml,
                camp: this.camp._meta.self,
                ...(this.activity ? { activity: this.activity._meta.self } : {}),
              }
        )
        this.textHtml = ''
        this.$emit('created')
      } catch (e) {
        this.errorMessages = [serverErrorToString(e)]
      } finally {
        this.saving = false
      }
    },
  },
}
</script>

<style scoped>
.ec-comment-composer__action {
  height: 36px;
  width: 36px;
  min-width: 36px;
}

.ec-comment-composer--reply :deep(.v-field.v-field--variant-outlined) {
  padding: 4px 4px 4px 12px;
}

.ec-comment-composer :deep(.v-field__append-inner) {
  padding-top: 0;
  flex-wrap: wrap;
  gap: 4px;
  align-content: flex-start;
  margin-left: 4px;
}
</style>
