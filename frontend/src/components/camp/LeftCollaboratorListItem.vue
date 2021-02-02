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
    <v-list-item-action class="ml-5">
      <icon-button color="normal"
                   icon="mdi-refresh"
                   text="inviteAgainText"
                   @click="api.patch(collaborator, {status: 'invited'})">
        {{ $tc("components.camp.leftCampCollaboratorListItem.inviteAgain") }}
      </icon-button>
    </v-list-item-action>
  </v-list-item>
</template>

<script>

import IconButton from '@/components/buttons/IconButton'

export default {
  name: 'LeftCollaboratorListItem',
  components: { IconButton },
  props: {
    collaborator: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
