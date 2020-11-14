<!--
Displays collaborators of a single camp.
-->
<template>
  <content-card :title="$tc('views.camp.collaborators.title')">
    <v-card-text>
      <content-group :title="$tc('views.camp.collaborators.members')">
        <v-list>
          <v-skeleton-loader v-if="collaborators.length <= 0" type="list-item-avatar-two-line@3" class="px-0" />
          <collaborator-list-item
            v-for="collaborator in establishedCollaborators"
            :key="collaborator._meta.self" :collaborator="collaborator" />
        </v-list>
      </content-group>

      <content-group v-if="requestedCollaborators.length > 0" :title="$tc('views.camp.collaborators.openRequests')">
        <v-list>
          <collaborator-list-item
            v-for="collaborator in requestedCollaborators"
            :key="collaborator._meta.self" :collaborator="collaborator" />
        </v-list>
      </content-group>

      <content-group v-if="invitedCollaborators.length > 0" :title="$tc('views.camp.collaborators.openInvitations')">
        <v-list>
          <collaborator-list-item
            v-for="collaborator in invitedCollaborators"
            :key="collaborator._meta.self" :collaborator="collaborator" />
        </v-list>
      </content-group>

      <content-group :title="$tc('views.camp.collaborators.invite')">
        <v-text-field
          v-model="search"
          hide-details
          prepend-icon="mdi-account-search"
          single-line
          :placeholder="$tc('views.camp.collaborators.search')"
          @focus="loadingResults = true"
          @blur="loadingResults = false" />

        <v-list>
          <v-skeleton-loader v-if="loadingResults && searchResults.length < 1" type="list-item-avatar-two-line@3" class="px-0" />
          <v-list-item v-for="result in searchResults" :key="result.id"
                       class="px-0" two-line>
            <v-list-item-avatar>
              <v-img src="https://i.pravatar.cc/300" />
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>{{ result.displayName }}</v-list-item-title>
              <v-list-item-subtitle>{{ result.mail }}</v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-action>
              <button-add icon="mdi-account-plus" @click="invite(result, 'member')">
                Member
              </button-add>
            </v-list-item-action>
            <v-list-item-action class="ml-1">
              <button-add icon="mdi-account-star" @click="invite(result, 'manager')">
                Manager
              </button-add>
            </v-list-item-action>
          </v-list-item>
        </v-list>
      </content-group>
    </v-card-text>
  </content-card>
</template>
<script>
import ContentCard from '@/components/layout/ContentCard'
import ContentGroup from '@/components/layout/ContentGroup'
import CollaboratorListItem from '@/components/camp/CollaboratorListItem'
import ButtonAdd from '@/components/buttons/ButtonAdd'

export default {
  name: 'Collaborators',
  components: {
    ButtonAdd,
    CollaboratorListItem,
    ContentGroup,
    ContentCard
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      editing: false,
      loadingResults: false,
      messages: [],
      search: ''
    }
  },
  computed: {
    collaborators () {
      return this.camp().campCollaborations().items
    },
    establishedCollaborators () {
      return this.collaborators.filter(c => c.status === 'established')
    },
    requestedCollaborators () {
      return this.collaborators.filter(c => c.status === 'requested')
    },
    invitedCollaborators () {
      return this.collaborators.filter(c => c.status === 'invited')
    },
    searchResults () {
      if (this.search.length >= 3) {
        const filterUserIds = [
          ...this.establishedCollaborators,
          ...this.requestedCollaborators,
          ...this.invitedCollaborators
        ].map(c => c.user().id)
        return this.api.get().users({ search: this.search }).items.filter(
          u => !filterUserIds.includes(u.id)
        )
      }
      return []
    }
  },
  created () {
    return this.camp().campCollaborations()
  },
  methods: {
    invite (user, role) {
      this.api.post('/camp-collaborations', {
        campId: this.camp().id,
        userId: user.id,
        role: role
      }).then(this.refreshCamp)
    },
    refreshCamp () {
      this.api.reload(this.camp()._meta.self)
    }
  }
}
</script>

<style lang="scss" scoped>
  ::v-deep .v-skeleton-loader__list-item-avatar-two-line {
    height: 72px;
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  ::v-deep .v-select__selections input {
    width: 20px;
  }
</style>
