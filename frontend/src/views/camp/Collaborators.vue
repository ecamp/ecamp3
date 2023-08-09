<!--
Displays collaborators of a single camp.
-->
<template>
  <content-card :title="$tc('views.camp.collaborators.title')" toolbar>
    <v-card-text>
      <ContentGroup :title="$tc('views.camp.collaborators.members')">
        <template #title-actions>
          <CollaboratorCreate v-if="isManager" :camp="camp()" />
        </template>
        <CollaboratorList
          :grouped-activities="groupedActivities"
          :collaborators="established"
          :is-manager="isManager"
        />
      </ContentGroup>

      <ContentGroup
        v-if="invited.length > 0"
        :title="$tc('views.camp.collaborators.openInvitations')"
      >
        <CollaboratorList
          :grouped-activities="groupedActivities"
          :collaborators="invited"
          :is-manager="isManager"
        />
      </ContentGroup>

      <ContentGroup
        v-if="inactive.length > 0"
        :title="$tc('views.camp.collaborators.inactiveCollaborators')"
      >
        <CollaboratorList
          :grouped-activities="groupedActivities"
          :collaborators="inactive"
          :is-manager="isManager"
          inactive
        />
      </ContentGroup>
    </v-card-text>
  </content-card>
</template>
<script>
import CollaboratorCreate from '@/components/collaborator/CollaboratorCreate.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import CollaboratorList from '@/components/collaborator/CollaboratorList.vue'
import { keyBy } from 'lodash'

export default {
  name: 'Collaborators',
  components: {
    CollaboratorList,
    CollaboratorCreate,
    ContentGroup,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Function, required: true },
  },
  computed: {
    collaborators() {
      return this.camp().campCollaborations().items
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
    activities() {
      return keyBy(this.camp().activities().items, '_meta.self')
    },
    /**
     * The Activities grouped by Camp-Collaborator
     * @return {Record<string, Record<string,Object>>}
     */
    groupedActivities() {
      const vals = {}
      for (let collaborator of this.collaborators) {
        const allColaboratorItems = []
        for (let activity of Object.values(this.activities)) {
          const items = Object.values(activity.activityResponsibles().items)
          for (let item of items) {
            const id = item.campCollaboration()._meta.self
            if (id === collaborator._meta.self) allColaboratorItems.push(activity)
          }
        }
        vals[collaborator._meta.self] = keyBy(allColaboratorItems, '_meta.self')
      }
      return vals
    },
  },
  created() {
    //return this.camp().campCollaborations()
  },
  async mounted() {
    return await Promise.all([
      this.camp().activities()._meta.load,
      this.camp().categories()._meta.load,
    ])
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
