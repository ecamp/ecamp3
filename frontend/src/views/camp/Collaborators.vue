<!--
Displays collaborators of a single camp.
-->
<template>
  <content-card :title="$tc('views.camp.collaborators.title')" toolbar>
    <v-card-text>
      <content-group :title="$tc('views.camp.collaborators.members')">
        <template #title-actions>
          <CollaboratorCreate v-if="isManager" :camp="camp()" />
        </template>
        <v-list class="mx-n2">
          <v-skeleton-loader
            v-if="collaborators.length <= 0"
            type="list-item-avatar-two-line@3"
            class="px-0"
          />
          <CollaboratorEdit
            v-for="collaborator in establishedCollaborators"
            v-else-if="isManager"
            :key="collaborator._meta.self"
            :collaborator="collaborator"
          />
          <CollaboratorListItem
            v-for="collaborator in establishedCollaborators"
            v-else
            :key="collaborator._meta.self"
            :collaborator="collaborator"
          />
        </v-list>
      </content-group>

      <ContentGroup
        v-if="invitedCollaborators.length > 0"
        :title="$tc('views.camp.collaborators.openInvitations')"
      >
        <v-list class="mx-n2">
          <template v-if="isManager">
            <CollaboratorEdit
              v-for="collaborator in invitedCollaborators"
              :key="collaborator._meta.self"
              :collaborator="collaborator"
            />
          </template>
          <CollaboratorListItem
            v-for="collaborator in invitedCollaborators"
            v-else
            :key="collaborator._meta.self"
            :collaborator="collaborator"
          />
        </v-list>
      </ContentGroup>

      <ContentGroup
        v-if="inactiveCollaborators.length > 0"
        :title="$tc('views.camp.collaborators.inactiveCollaborators')"
      >
        <v-list class="mx-n2">
          <template v-if="isManager">
            <CollaboratorEdit
              v-for="collaborator in inactiveCollaborators"
              :key="collaborator._meta.self"
              :collaborator="collaborator"
              inactive
            />
          </template>
          <CollaboratorListItem
            v-for="collaborator in inactiveCollaborators"
            v-else
            :key="collaborator._meta.self"
            :collaborator="collaborator"
            inactive
          />
        </v-list>
      </ContentGroup>
    </v-card-text>
  </content-card>
</template>
<script>
import CollaboratorCreate from '@/components/collaborator/CollaboratorCreate.vue'
import CollaboratorEdit from '@/components/collaborator/CollaboratorEdit.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import { transformViolations } from '@/helpers/serverError'
import CollaboratorListItem from '@/components/collaborator/CollaboratorListItem.vue'

const DEFAULT_INVITE_ROLE = 'member'

export default {
  name: 'Collaborators',
  components: {
    CollaboratorListItem,
    CollaboratorCreate,
    CollaboratorEdit,
    ContentGroup,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Function, required: true },
  },
  data() {
    return {
      editing: false,
      inviteEmailMessages: [],
      inviteEmail: '',
      inviteRole: DEFAULT_INVITE_ROLE,
      isSaving: false,
    }
  },
  computed: {
    collaborators() {
      return this.camp().campCollaborations().items
    },
    establishedCollaborators() {
      return this.collaborators.filter((c) => c.status === 'established')
    },
    invitedCollaborators() {
      return this.collaborators.filter((c) => c.status === 'invited')
    },
    inactiveCollaborators() {
      return this.collaborators.filter((c) => c.status === 'inactive')
    },
  },
  created() {
    return this.camp().campCollaborations()
  },
  methods: {
    invite() {
      this.isSaving = true
      this.api
        .href(this.api.get(), 'campCollaborations')
        .then((url) =>
          this.api.post(url, {
            camp: this.camp()._meta.self,
            inviteEmail: this.inviteEmail,
            role: this.inviteRole,
          })
        )
        .then(this.refreshCamp, this.handleError)
        .finally(() => {
          this.isSaving = false
        })
    },
    handleError(e) {
      const violations = transformViolations(e, this.$i18n)
      this.inviteEmailMessages = Object.values(violations).flatMap(
        (violationsOfProperty) => violationsOfProperty
      )
    },
    refreshCamp() {
      this.inviteEmail = null
      this.inviteRole = DEFAULT_INVITE_ROLE
      this.$refs.validation.reset()
      this.clearMessages()
      this.api.reload(this.camp()._meta.self)
    },
    clearMessages() {
      this.inviteEmailMessages = []
    },
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
