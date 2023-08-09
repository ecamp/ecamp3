<template>
  <v-list-item class="px-2 rounded e-collaborator-item" two-line v-on="$listeners">
    <v-list-item-action>
      <user-avatar size="40" :camp-collaboration="collaborator" omit-sr />
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title>
        {{ name }}
      </v-list-item-title>
      <v-list-item-subtitle>
        <v-tooltip right>
          <template #activator="{ on }">
            <button v-on="on">
              {{ $tc(roles[collaborator.role].roleTranslation)
              }}<span>
                &middot;
                <template v-for="icon in roles[collaborator.role].icons"
                  ><v-icon :key="icon" x-small class="vertical-baseline">{{
                    icon
                  }}</v-icon
                  >&thinsp;</template
                ></span
              >
            </button>
          </template>
          {{ $tc(roles[collaborator.role].abilitiesTranslation) }}
        </v-tooltip>
        <div class="responsibilities">
          <div>
            {{ $tc('components.collaborator.collaboratorListItem.responsibilities') }}
          </div>
          <GenericChip :dense="true">{{ activityList?.length }}</GenericChip>
        </div>
      </v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-action class="e-collaborator-item__actions ml-2">
      <button-edit
        v-if="editable"
        color="primary--text"
        text
        class="my-n1 v-btn--has-bg"
      />
    </v-list-item-action>
  </v-list-item>
</template>

<script>
import UserAvatar from '@/components/user/UserAvatar.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import GenericChip from '@/components/generic/GenericChip.vue'

export default {
  name: 'CollaboratorListItem',
  components: {
    GenericChip,
    ButtonEdit,
    UserAvatar,
  },
  props: {
    collaborator: { type: Object, required: true },
    editable: { type: Boolean, default: false },
    activities: { type: Object, required: false, default: () => ({}) },
  },
  data: () => ({
    roles: {
      manager: {
        roleTranslation: 'entity.camp.collaborators.manager',
        abilitiesTranslation: 'global.collaborationAbilities.manager',
        icons: ['mdi-eye-outline', 'mdi-pencil-outline', 'mdi-cog-outline'],
      },
      member: {
        roleTranslation: 'entity.camp.collaborators.member',
        abilitiesTranslation: 'global.collaborationAbilities.member',
        icons: ['mdi-eye-outline', 'mdi-pencil-outline'],
      },
      guest: {
        roleTranslation: 'entity.camp.collaborators.guest',
        abilitiesTranslation: 'global.collaborationAbilities.guest',
        icons: ['mdi-eye-outline'],
      },
    },
  }),
  computed: {
    name() {
      return this.collaborator.user
        ? this.collaborator.user().displayName
        : this.collaborator.inviteEmail
    },
    activityList() {
      return Object.values(this.activities)
    },
  },
}
</script>

<style scoped>
.v-list-item--link:before {
  border-radius: 6px;
}
.v-list-item__subtitle {
  display: flex;
  flex-direction: row;
  align-content: space-between;
  align-items: baseline;
  justify-content: space-between;
}
.responsibilities {
  display: flex;
  flex-direction: row;
  align-items: baseline;
  gap: 1rem;
  margin-right: 2rem;
  font-size: small;
}
</style>
