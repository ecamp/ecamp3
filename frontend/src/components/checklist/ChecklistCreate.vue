<template>
  <DetailPane
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-account-plus"
    :title="$tc('components.checklist.checklistCreate.title')"
    :submit-action="createChecklist"
    :submit-label="$tc('global.button.create')"
    submit-icon="mdi-plus"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="{ on }">
      <ButtonAdd
        color="secondary"
        text
        class="my-n2"
        icon="mdi-playlist-plus"
        v-on="on"
        >{{ $tc('components.checklist.checklistCreate.title') }}</ButtonAdd
      >
    </template>

    <e-text-field
      v-model="entityData.name"
      type="text"
      path="name"
      vee-rules="required"
    />
  </DetailPane>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import DetailPane from '@/components/generic/DetailPane.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'

export default {
  name: 'ChecklistCreate',
  components: { ButtonAdd, DetailPane },
  extends: DialogBase,
  provide() {
    return {
      entityName: 'checklist',
    }
  },
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['camp', 'name'],
      entityUri: '',
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          name: '',
          camp: this.camp._meta.self,
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    },
  },
  mounted() {
    this.api.href(this.api.get(), 'checklists').then((uri) => (this.entityUri = uri))
  },
  methods: {
    createChecklist() {
      return this.create().then(() => {
        this.api.reload(this.camp.checklists())
      })
    },
  },
}
</script>

<style scoped></style>
