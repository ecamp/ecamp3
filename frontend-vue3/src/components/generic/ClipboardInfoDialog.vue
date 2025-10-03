<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-content-copy"
    :title="title"
    :cancel-action="cancel"
    :cancel-label="$t('global.button.close')"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <p>{{ description }}</p>
    <p v-if="clipboardReadState === 'prompt'">
      <center>
        <v-btn color="success" @click="requestClipboardAccess">{{ allow }}</v-btn>
      </center>
    </p>
    <p v-if="clipboardReadState === 'granted'">{{ granted }}</p>
    <p v-if="clipboardReadState === 'denied'">{{ denied }}</p>
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'

export default {
  name: 'ClipboardInfoDialog',
  components: {
    DialogForm,
  },
  extends: DialogBase,
  props: {
    translationContextI18nKey: { type: String, required: true },
  },
  data() {
    return {
      clipboardReadState: 'unknown',
    }
  },
  computed: {
    title() {
      return this.$t(this.translationContextI18nKey + '.title')
    },
    description() {
      return this.$t(this.translationContextI18nKey + '.description')
    },
    allow() {
      return this.$t(this.translationContextI18nKey + '.allow')
    },
    granted() {
      return this.$t(this.translationContextI18nKey + '.granted')
    },
    denied() {
      return this.$t(this.translationContextI18nKey + '.denied')
    },
  },
  async mounted() {
    try {
      // read current permission
      const res = await navigator.permissions.query({ name: 'clipboard-read' })
      this.clipboardReadState = res.state
    } catch {
      console.warn('clipboard permission not requestable')
    }
  },
  methods: {
    cancel() {
      this.close()
    },
    async requestClipboardAccess() {
      // if permission is not yet requested, request it
      if (this.clipboardReadState === 'prompt') {
        try {
          await navigator.clipboard.readText()
        } catch {
          console.log('clipboard read is denied')
        }

        const res = await navigator.permissions.query({ name: 'clipboard-read' })
        this.clipboardReadState = res.state
      }
    },
  },
}
</script>
