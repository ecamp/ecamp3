<template>
  <DetailPane
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-playlist-plus"
    :title="$t('components.checklist.checklistCreate.title')"
    :submit-action="createChecklist"
    :submit-label="$t('global.button.create')"
    submit-icon="mdi-plus"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="{ props }">
      <ButtonAdd
        color="blue-grey-darken-2"
        variant="text"
        icon="mdi-playlist-plus"
        v-bind="props"
        @click="showDialog = true"
        >{{ $t('components.checklist.checklistCreate.title') }}</ButtonAdd
      >
    </template>

    <e-text-field
      v-model="entityData.name"
      type="text"
      path="name"
      vee-rules="required"
    />

    <e-autocomplete
      v-model="entityData.copyChecklistSource"
      path="copyChecklistSource"
      item-title="text"
      item-value="value"
      clearable
      :items="prototypeChecklists"
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
    camp: { type: Object, required: false, default: null },
    checklistCollection: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['camp', 'name', 'copyChecklistSource', 'isPrototype'],
      entityUri: '',
    }
  },
  computed: {
    prototypeChecklists() {
      return this.api
        .get()
        .checklists({ isPrototype: true })
        .items.map((c) => ({
          value: c._meta.self,
          text: c.name,
          object: c,
        }))
    },
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          camp: this.camp?._meta.self,
          name: '',
          copyChecklistSource: null,
          isPrototype: this.camp ? false : true,
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
        this.api.reload(this.checklistCollection)
      })
    },
  },
}
</script>

<style scoped></style>
