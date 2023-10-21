<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-content-copy"
    :title="$tc('components.activity.copyActivityInfoDialog.title')"
    :cancel-action="cancel"
    :cancel-label="$tc('global.button.close')"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <p>
      {{ $tc('components.activity.copyActivityInfoDialog.description') }}
    </p>
    <p v-if="clipboardReadState === 'prompt'">
      <center>
        <v-btn color="success" @click="requestClipboardAccess">
          {{ $tc('components.activity.copyActivityInfoDialog.allow') }}
        </v-btn>
      </center>
    </p>
    <p v-if="clipboardReadState === 'granted'">
      {{ $tc('components.activity.copyActivityInfoDialog.granted') }}
    </p>
    <p v-if="clipboardReadState === 'denied'">
      {{ $tc('components.activity.copyActivityInfoDialog.denied') }}
    </p>
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'

export default {
  name: 'CopyActivityInfoDialog',
  components: {
    DialogForm,
  },
  extends: DialogBase,
  data() {
    return {
      clipboardReadState: 'unknown',
    }
  },
  async mounted() {
    // read current permission
    const res = await navigator.permissions.query({ name: 'clipboard-read' })
    this.clipboardReadState = res.state
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
