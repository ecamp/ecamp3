<template>
  <v-list-item class="px-0" two-line>
    <v-list-item-avatar>
      <v-img src="https://i.pravatar.cc/300" />
    </v-list-item-avatar>
    <v-list-item-content>
      <v-list-item-title>{{ collaborator.user().displayName }}</v-list-item-title>
      <v-list-item-subtitle>{{ collaborator.user().mail }}</v-list-item-subtitle>
    </v-list-item-content>
    <api-select
      :value="collaborator.role"
      :uri="collaborator._meta.self"
      fieldname="role"
      :items="['member', 'manager']"
      :my="0"
      dense
      vee-rules="required" />
    <v-list-item-action class="ml-2">
      <button-delete @click="api.del(collaborator)">
        Remove
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
