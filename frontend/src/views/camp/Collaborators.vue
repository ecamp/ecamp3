<!--
Displays collaborators of a single camp.
-->
<template>
  <content-card :title="$tc('views.camp.collaborators.title')" toolbar>
    <v-card-text>
      <content-group :title="$tc('views.camp.collaborators.members')">
        <v-list>
          <v-skeleton-loader
            v-if="collaborators.length <= 0"
            type="list-item-avatar-two-line@3"
            class="px-0"
          />
          <collaborator-list-item
            v-for="collaborator in establishedCollaborators"
            :key="collaborator._meta.self"
            :collaborator="collaborator"
            :disabled="!isManager"
          />
        </v-list>
      </content-group>

      <content-group
        v-if="invitedCollaborators.length > 0"
        :title="$tc('views.camp.collaborators.openInvitations')"
      >
        <v-list>
          <collaborator-list-item
            v-for="collaborator in invitedCollaborators"
            :key="collaborator._meta.self"
            :collaborator="collaborator"
            :disabled="!isManager"
          />
        </v-list>
      </content-group>

      <content-group
        v-if="inactiveCollaborators.length > 0"
        :title="$tc('views.camp.collaborators.inactiveCollaborators')"
      >
        <v-list>
          <collaborator-list-item
            v-for="collaborator in inactiveCollaborators"
            :key="collaborator._meta.self"
            :collaborator="collaborator"
            :disabled="!isManager"
            inactive
          />
        </v-list>
      </content-group>
    </v-card-text>
    <v-divider class="mt-n6" />
    <v-card-text>
      <div class="text-right">
        <dialog-collaborator-create v-if="isManager" :camp="camp()">
          <template #activator="{ on }">
            <button-add color="success" icon="mdi-account-plus" v-on="on">
              {{ $tc('views.camp.collaborators.invite') }}
            </button-add>
          </template>
        </dialog-collaborator-create>
      </div>
    </v-card-text>
  </content-card>
</template>
<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import CollaboratorListItem from '@/components/collaborator/CollaboratorListItem.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import { transformViolations } from '@/helpers/serverError'
import DialogCollaboratorCreate from '@/components/collaborator/DialogCollaboratorCreate.vue'

const DEFAULT_INVITE_ROLE = 'member'

export default {
  name: 'Collaborators',
  components: {
    DialogCollaboratorCreate,
    ButtonAdd,
    CollaboratorListItem,
    ContentGroup,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Function, required: true },
  },
  data () {
    return {
      editing: false,
      inviteEmailMessages: [],
      inviteEmail: '',
      inviteRole: DEFAULT_INVITE_ROLE,
      isSaving: false,
    }
  },
  computed: {
    collaborators () {
      return this.camp().campCollaborations().items
    },
    establishedCollaborators () {
      return this.collaborators.filter((c) => c.status === 'established')
    },
    invitedCollaborators () {
      return this.collaborators.filter((c) => c.status === 'invited')
    },
    inactiveCollaborators () {
      return this.collaborators.filter((c) => c.status === 'inactive')
    },
  },
  created () {
    return this.camp().campCollaborations()
  },
  methods: {
    invite () {
      this.isSaving = true
      this.api
        .href(this.api.get(), 'campCollaborations')
        .then((url) =>
          this.api.post(url, {
            camp: this.camp()._meta.self,
            inviteEmail: this.inviteEmail,
            role: this.inviteRole,
          }),
        )
        .then(this.refreshCamp, this.handleError)
        .finally(() => {
          this.isSaving = false
        })
    },
    handleError (e) {
      const violations = transformViolations(e, this.$i18n)
      this.inviteEmailMessages = Object.values(violations).flatMap(
        (violationsOfProperty) => violationsOfProperty,
      )
    },
    refreshCamp () {
      this.inviteEmail = null
      this.inviteRole = DEFAULT_INVITE_ROLE
      this.$refs.validation.reset()
      this.clearMessages()
      this.api.reload(this.camp()._meta.self)
    },
    clearMessages () {
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
