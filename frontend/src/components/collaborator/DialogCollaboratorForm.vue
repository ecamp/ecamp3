<template>
  <e-select
    :v-model="localCollaboration.role"
    :name="$tc('entity.campCollaboration.fields.role')"
    fieldname="role"
    :items="[
      { key: 'member', translation: $tc('entity.camp.collaborators.member') },
      { key: 'manager', translation: $tc('entity.camp.collaborators.manager') },
      { key: 'guest', translation: $tc('entity.camp.collaborators.guest') },
    ]"
    :hint="$tc('components.collaborator.dialogCollaboratorForm.roleHint')"
    persistent-hint
    item-value="key"
    item-text="translation"
    :my="0"
    vee-rules="required"
  />
</template>

<script>
export default {
  name: 'DialogCollaboratorForm',
  props: {
    collaboration: { type: Object, required: true },
  },
  computed: {
    localCollaboration() {
      return this.collaboration
    },
  },
}
</script>
