<template>
  <v-list-item
    class="px-2 rounded e-collaborator-item"
    lines="two"
    v-bind="$attrs"
    :title="name"
  >
    <template #prepend>
      <user-avatar size="40" :camp-collaboration="collaborator" omit-sr />
    </template>
    <v-list-item-subtitle>
      <v-tooltip location="right">
        <template #activator="{ props }">
          <button v-bind="props">
            {{ $t(roles[collaborator.role].roleTranslation)
            }}<span>
              &middot;
              <template v-for="icon in roles[collaborator.role].icons" :key="icon"
                ><v-icon class="vertical-baseline" :size="null">{{ icon }}</v-icon
                >&thinsp;</template
              ></span
            >
          </button>
        </template>
        {{ $t(roles[collaborator.role].abilitiesTranslation) }}
      </v-tooltip>
    </v-list-item-subtitle>
    <template #append>
      <v-list-item-action class="e-collaborator-item__actions ml-2">
        <button-edit v-if="editable" color="primary" variant="tonal" class="my-n1" />
      </v-list-item-action>
    </template>
  </v-list-item>
</template>

<script>
import UserAvatar from '@/components/user/UserAvatar.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import campCollaborationDisplayName from '../../../../common/helpers/campCollaborationDisplayName'

export default {
  name: 'CollaboratorListItem',
  components: {
    ButtonEdit,
    UserAvatar,
  },
  props: {
    collaborator: { type: Object, required: true },
    editable: { type: Boolean, default: false },
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
      return campCollaborationDisplayName(this.collaborator, this.$t.bind(this), false)
    },
  },
}
</script>

<style scoped>
.v-list-item--link:before {
  border-radius: 6px;
}
</style>
