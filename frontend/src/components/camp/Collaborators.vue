<!--
Displays collaborators of a single camp.
-->
<template>
  <content-card title="Team">
    <v-card-text>
      <content-group title="Mitglieder">
        <v-list>
          <collaborator-list-item
            v-for="collaborator in establishedCollaborators"
            :key="collaborator._meta.self" :collaborator="collaborator" />
        </v-list>
      </content-group>

      <content-group v-if="requestedCollaborators.length > 0" title="Offene Anfragen">
        <v-list>
          <collaborator-list-item
            v-for="collaborator in requestedCollaborators"
            :key="collaborator._meta.self" :collaborator="collaborator" />
        </v-list>
      </content-group>

      <content-group v-if="invitedCollaborators.length > 0" title="Offene Einladungen">
        <v-list>
          <collaborator-list-item
            v-for="collaborator in invitedCollaborators"
            :key="collaborator._meta.self" :collaborator="collaborator" />
        </v-list>
      </content-group>

      <content-group title="Einladen">
        <v-text-field
          v-model="search"
          hide-details
          prepend-icon="mdi-account-search"
          single-line
          placeholder="Suchen" />

        <v-list>
          <v-list-item v-for="result in searchResults" :key="result.id"
                       class="px-0" two-line>
            <v-list-item-avatar>
              <v-img src="https://i.pravatar.cc/300" />
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>{{ result.username }}</v-list-item-title>
              <v-list-item-subtitle>{{ result.mail }}</v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-action>
              <v-btn
                small
                color="success"
                class="mr-1"
                @click="invite(result, 'member')">
                <v-icon
                  small
                  left>
                  mdi-plus-circle
                </v-icon>
                member
              </v-btn>
            </v-list-item-action>
            <v-list-item-action>
              <v-btn
                small
                color="success"
                @click="invite(result, 'manager')">
                <v-icon
                  small
                  left>
                  mdi-plus-circle
                </v-icon>
                manager
              </v-btn>
            </v-list-item-action>
          </v-list-item>
        </v-list>
      </content-group>
    </v-card-text>
  </content-card>
</template>
<script>
import ContentCard from '@/components/base/ContentCard'
import ContentGroup from '@/components/base/ContentGroup'
import CollaboratorListItem from '@/components/camp/CollaboratorListItem'

export default {
  name: 'Collaborators',
  components: {
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
      messages: [],
      search: ''
    }
  },
  computed: {
    collaborators () {
      return this.camp().camp_collaborations().items.filter(c => !c._meta.deleting)
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
        const filterUsers = this.collaborators.filter(
          c => c.user !== undefined
        ).map(
          c => c.user().id
        )
        return this.api.get('/user?search=' + this.search).items.filter(
          u => !filterUsers.includes(u.id)
        )
      }
      return []
    }
  },
  methods: {
    changeStatus (collaborator, status) {
      this.api.patch(collaborator, { status: status })
    },
    invite (user, role) {
      this.api.post('/camp-collaboration', {
        camp_id: this.camp().id,
        user_id: user.id,
        role: role
      }).then(this.refreshCamp)
    },
    refreshCamp () {
      this.api.reload(this.campUri)
    }
  }
}
</script>
