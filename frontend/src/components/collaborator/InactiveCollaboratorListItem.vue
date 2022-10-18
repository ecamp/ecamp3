<template>
  <v-list-item class="px-0" two-line>
    <v-list-item-avatar>
      <user-avatar :camp-collaboration="collaborator" />
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
      <icon-button
        color="normal"
        icon="mdi-refresh"
        :disabled="disabled"
        @click="api.patch(collaborator, { status: 'invited' })"
      >
        {{ $tc('components.collaborator.inactiveCollaboratorListItem.inviteAgain') }}
      </icon-button>
    </v-list-item-action>
    <v-list-item-action>
      <dialog-entity-delete :entity="collaborator">
        <template #activator="{ on }">
          <button-delete :disabled="disabled" v-on="on" />
        </template>
        {{ $tc('components.collaborator.inactiveCollaboratorListItem.delete') }} <br />
        <ul>
          <li>
            <span v-if="collaborator.user">
              {{ collaborator.user().displayName }}
            </span>
            <span v-else>
              {{ collaborator.inviteEmail }}
            </span>
          </li>
        </ul>
      </dialog-entity-delete>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
import IconButton from '@/components/buttons/IconButton.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import UserAvatar from '@/components/user/UserAvatar.vue'

export default {
  name: 'InactiveCollaboratorListItem',
  components: { IconButton, ButtonDelete, DialogEntityDelete, UserAvatar },
  props: {
    collaborator: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
  },
}
</script>

<style scoped></style>
