<!--
Displays collaborators of a single camp.
-->
<template>
  <content-card :title="$t('views.camp.admin.collaborators.title')" toolbar>
    <template v-if="showHitobitoInvite" #title-actions>
      <CollaboratorHitobitoInvite :camp="camp" />
    </template>
    <v-card-text>
      <ContentGroup :title="$t('views.camp.admin.collaborators.members')">
        <template #title-actions>
          <CollaboratorCreate v-if="isManager" :camp="camp" />
          <CollaboratorBulkCreate v-if="isManager" :camp="camp" />
        </template>
        <CollaboratorList :collaborators="established" :is-manager="isManager" />
      </ContentGroup>

      <ContentGroup
        v-if="invited.length > 0"
        :title="$t('views.camp.admin.collaborators.openInvitations')"
      >
        <CollaboratorList :collaborators="invited" :is-manager="isManager" />
      </ContentGroup>

      <ContentGroup
        v-if="inactive.length > 0"
        :title="$t('views.camp.admin.collaborators.inactiveCollaborators')"
      >
        <CollaboratorList :collaborators="inactive" :is-manager="isManager" inactive />
      </ContentGroup>
    </v-card-text>
  </content-card>
</template>
<script>
import CollaboratorBulkCreate from '@/components/collaborator/CollaboratorBulkCreate.vue'
import CollaboratorCreate from '@/components/collaborator/CollaboratorCreate.vue'
import CollaboratorHitobitoInvite from '@/components/collaborator/CollaboratorHitobitoInvite.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import { isValidProvider } from '@/plugins/hitobito.js'
import CollaboratorList from '@/components/collaborator/CollaboratorList.vue'

export default {
  name: 'CampAdminCollaborators',
  components: {
    CollaboratorList,
    CollaboratorCreate,
    CollaboratorBulkCreate,
    CollaboratorHitobitoInvite,
    ContentGroup,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Object, required: true },
  },
  head() {
    return {
      title: this.$t('views.camp.admin.collaborators.title'),
    }
  },
  computed: {
    showHitobitoInvite() {
      return (
        this.isManager &&
        isValidProvider(this.camp.hitobitoProvider) &&
        !!this.camp.hitobitoEventId
      )
    },
    collaborators() {
      return this.camp.campCollaborations().items
    },
    established() {
      return this.collaborators.filter((c) => c.status === 'established')
    },
    invited() {
      return this.collaborators.filter((c) => c.status === 'invited')
    },
    inactive() {
      return this.collaborators.filter((c) => c.status === 'inactive')
    },
  },
  created() {
    return this.camp.campCollaborations()
  },
}
</script>

<style lang="scss" scoped>
:deep(.v-skeleton-loader__list-item-avatar-two-line) {
  height: 72px;
  padding-left: 0 !important;
  padding-right: 0 !important;
}

:deep(.v-select__selections input) {
  width: 20px;
}
</style>
