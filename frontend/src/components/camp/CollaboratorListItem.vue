<template>
  <v-list-item class="px-0" two-line>
    <v-list-item-avatar>
      <v-img src="https://i.pravatar.cc/300" />
    </v-list-item-avatar>
    <v-list-item-content>
      <v-list-item-title>{{ collaborator.user().username }}</v-list-item-title>
      <v-list-item-subtitle>
        {{
          collaborator.user().mail
        }}
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
      <button-delete @click="api.del(collaborator)">
        {{ $tc("components.camp.collaborators.remove") }}
      </button-delete>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
import ApiSelect from '@/components/form/api/ApiSelect'

import ButtonDelete from '@/components/buttons/ButtonDelete'

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
