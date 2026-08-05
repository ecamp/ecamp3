<template>
  <div
    class="px-3 py-2"
    data-testid="comment-composer"
    @keydown.capture.ctrl.enter.prevent.stop="submit"
    @keydown.capture.meta.enter.prevent.stop="submit"
  >
    <ERichtext
      v-model="textHtml"
      vee-id="textHtml"
      :placeholder="$t('components.comments.commentComposer.placeholder')"
      :error-messages="errorMessages"
      :disabled="saving"
      variant="outlined"
      density="compact"
    >
      <template #append-inner>
        <v-btn
          icon="mdi-send"
          variant="text"
          density="comfortable"
          color="primary"
          class="align-self-end"
          :disabled="!textHtml"
          :loading="saving"
          :aria-label="$t('global.button.submit')"
          data-testid="comment-submit"
          @click="submit"
        />
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
    camp: { type: Object, required: true },
    activity: { type: Object, default: null },
  },
  emits: ['created'],
  data() {
    return {
      textHtml: '',
      saving: false,
      errorMessages: [],
    }
  },
  methods: {
    async submit() {
      if (!this.textHtml || this.saving) return
      this.saving = true
      this.errorMessages = []
      try {
        await this.api.post(await this.api.href(this.api.get(), 'comments'), {
          textHtml: this.textHtml,
          camp: this.camp._meta.self,
          ...(this.activity ? { activity: this.activity._meta.self } : {}),
        })
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
