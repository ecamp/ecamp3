<template>
  <v-list-item class="px-0" two-line>
    <v-list-item-avatar>
      <user-avatar :value="collaborator" />
    </v-list-item-avatar>
    <v-list-item-content>
      <v-list-item-title v-if="collaborator.user">
        {{ collaborator.user().displayName }}
      </v-list-item-title>
      <v-list-item-subtitle v-else>
        {{ collaborator.inviteEmail }}
      </v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-action
      v-if="collaborator.status === 'invited'"
      class="ml-2">
      <icon-button
        color="normal"
        icon="mdi-refresh"
        :animate="resendingEmail"
        @click="resendInvitation">
        {{ $tc('components.camp.collaboratorListItem.resendEmail') }}
      </icon-button>
    </v-list-item-action>
    <v-list-item-action class="ml-4">
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
    </v-list-item-action>
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
import UserAvatar from '../user/UserAvatar.vue'

export default {
  name: 'CollaboratorListItem',
  components: { ButtonDelete, ApiSelect, UserAvatar },
  props: {
    collaborator: { type: Object, required: true }
  },
  data: () => ({
    resendingEmail: false
  }),
  methods: {
    resendInvitation () {
      this.resendingEmail = true
      this.api.href(this.api.get(), 'invitation', {
        action: 'resend',
        campCollaborationId: this.collaborator.id
      }).then(postUrl => this.api.post(postUrl, {}))
        .finally(() => { this.resendingEmail = false })
    }
  }
}
</script>

<style scoped>
</style>
