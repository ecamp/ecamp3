<template>
  <v-list-item class="px-0" two-line>
    <v-list-item-avatar>
      <v-img src="https://i.pravatar.cc/300" />
    </v-list-item-avatar>
    <v-list-item-content>
      <v-list-item-title>{{ collaborator.user().username }}</v-list-item-title>
      <v-list-item-subtitle>{{ collaborator.user().mail }}</v-list-item-subtitle>
    </v-list-item-content>
    <api-select
      :value="collaborator.role"
      :uri="collaborator._meta.self"
      fieldname="role"
      :items="['member', 'manager']"
      required
      dense />
    <v-list-item-action>
      <v-btn
        small
        color="warning"
        @click="api.del(collaborator)">
        <v-icon
          small
          left>
          mdi-close
        </v-icon>
        delete
      </v-btn>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
import ApiSelect from '@/components/form/ApiSelect'

export default {
  name: 'CollaboratorListItem',
  components: { ApiSelect },
  props: {
    collaborator: { type: Object, required: true }
  }
}
</script>

<style scoped>

</style>
