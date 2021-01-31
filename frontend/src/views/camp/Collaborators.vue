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

      <content-group v-if="leftCollaborators.length > 0" :title="$tc('views.camp.collaborators.leftCollaborators')">
        <v-list>
          <left-collaborator-list-item
            v-for="collaborator in leftCollaborators"
            :key="collaborator._meta.self" :collaborator="collaborator" />
        </v-list>
      </content-group>

      <content-group :title="$tc('views.camp.collaborators.invite')">
        <v-form @submit.prevent="invite">
          <v-container>
            <v-row
              align="center">
              <v-col>
                <e-text-field
                  v-model="inviteEmail"
                  :error-messages="inviteEmailMessages"
                  single-line
                  aria-autocomplete="none"
                  :placeholder="$tc('views.camp.collaborators.email')" />
              </v-col>
              <v-col
                sm="12"
                md="3">
                <e-select
                  :value="inviteRole"
                  :items="[
                    { key: 'member', translation: $tc('entity.camp.collaborators.member') },
                    { key: 'manager', translation: $tc('entity.camp.collaborators.manager') },
                  ]"
                  item-value="key"
                  item-text="translation"
                  :my="0"
                  dense
                  vee-rules="required" />
              </v-col>
              <v-col>
                <button-add type="submit" icon="mdi-account-plus">
                  {{ $tc('views.camp.collaborators.invite') }}
                </button-add>
              </v-col>
            </v-row>
          </v-container>
        </v-form>
      </content-group>
    </v-card-text>
  </content-card>
</template>
<script>
import ContentCard from '@/components/layout/ContentCard'
import ContentGroup from '@/components/layout/ContentGroup'
import CollaboratorListItem from '@/components/camp/CollaboratorListItem'
import ButtonAdd from '@/components/buttons/ButtonAdd'
import ETextField from '@/components/form/base/ETextField'
import ESelect from '@/components/form/base/ESelect'
import LeftCollaboratorListItem from '@/components/camp/LeftCollaboratorListItem'

export default {
  name: 'Collaborators',
  components: {
    ButtonAdd,
    CollaboratorListItem,
    ContentGroup,
    ContentCard,
    ETextField,
    ESelect,
    LeftCollaboratorListItem
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      editing: false,
      messages: [],
      inviteEmail: '',
      inviteRole: 'member'
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
    leftCollaborators () {
      return this.collaborators.filter(c => c.status === 'left')
    },
    inviteEmailMessages () {
      return this.messages.inviteEmail ? Object.values({ ...this.messages.inviteEmail }) : []
    }
  },
  created () {
    return this.camp().campCollaborations()
  },
  methods: {
    invite () {
      this.api.post('/camp-collaborations', {
        campId: this.camp().id,
        inviteEmail: this.inviteEmail,
        role: this.inviteRole
      }).then(this.refreshCamp,
        this.handleError)
    },
    handleError (e) {
      if (e.response) {
        if (e.response.status === 409 /* Conflict */) {
          this.messages = [this.$tc('global.serverError.409')]
        }
        if (e.response.status === 422 /* Validation Error */) {
          this.messages = e.response.data.validation_messages
        }
      }
    },
    refreshCamp () {
      this.messages = []
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
