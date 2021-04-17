<template>
  <v-list-item class="px-0" two-line>
    <v-list-item-avatar>
      <v-img src="https://i.pravatar.cc/300" />
    </v-list-item-avatar>
    <v-list-item-content>
      <v-list-item-title v-if="collaborator.user">
        {{ collaborator.user().displayName }}
      </v-list-item-title>
      <v-list-item-subtitle v-else>
        {{ collaborator.inviteEmail }}
      </v-list-item-subtitle>
    </v-list-item-content>
    <api-select
      :value="collaborator.role"
      :uri="collaborator._meta.self"
      fieldname="role"
      :items="[
        { key: 'member', translation: $tc('entity.camp.collaborators.member') },
        { key: 'manager', translation: $tc('entity.camp.collaborators.manager') },
      ]"
      item-value="key"
      item-text="translation"
      :my="0"
      dense
      vee-rules="required" />
    <v-list-item-action class="ml-2">
      <button-delete
        icon="mdi-cancel"
        @click="api.del(collaborator)">
        {{ $tc("components.camp.collaboratorListItem.deactivate") }}
      </button-delete>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
import ApiSelect from '@/components/form/api/ApiSelect.vue'

import ButtonDelete from '@/components/buttons/ButtonDelete.vue'

export default {
  name: 'CollaboratorListItem',
  components: { ButtonDelete, ApiSelect },
  props: {
    collaborator: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
