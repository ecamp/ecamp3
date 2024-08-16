<template>
  <DetailPane
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-account-plus"
    :title="$tc('components.checklist.checklistItemCreate.title')"
    :submit-action="createChecklistItem"
    :submit-label="$tc('global.button.add')"
    submit-icon="mdi-plus"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="{ on }">
      <slot name="activator" v-bind="{ on }">
        <ButtonAdd color="secondary" text class="my-n2" icon="mdi-playlist-plus" v-on="on"
          >{{ $tc('components.checklist.checklistItemCreate.title') }}
        </ButtonAdd>
      </slot>
    </template>

    <e-text-field
      v-model="entityData.text"
      type="text"
      path="text"
      vee-rules="required"
      autofocus
    />
  </DetailPane>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import DetailPane from '@/components/generic/DetailPane.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'

export default {
  name: 'ChecklistItemCreate',
  components: { ButtonAdd, DetailPane },
  extends: DialogBase,
  provide() {
    return {
      entityName: 'checklistItem',
    }
  },
  props: {
    checklist: { type: Object, required: true },
    parent: { type: String, default: null },
  },
  data() {
    return {
      entityProperties: ['checklist', 'text', 'parent'],
      entityUri: '',
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          text: '',
          checklist: this.checklist._meta.self,
          parent: this.parent,
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    },
  },
  mounted() {
    this.api.href(this.api.get(), 'checklistItems').then((uri) => (this.entityUri = uri))
  },
  methods: {
    createChecklistItem() {
      return this.create().then(() => {
        this.api.reload(this.checklist.checklistItems())
      })
    },
  },
}
</script>

<style scoped></style>
