<!--
Displays collaborators of a single camp.
-->
<template>
  <content-card :title="$tc('views.camp.collaborators.title')">
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
          <inactive-collaborator-list-item
            v-for="collaborator in inactiveCollaborators"
            :key="collaborator._meta.self"
            :collaborator="collaborator"
            :disabled="!isManager"
          />
        </v-list>
      </content-group>

      <content-group v-if="isManager" :title="$tc('views.camp.collaborators.invite')">
        <v-form @submit.prevent="invite">
          <v-container>
            <v-row align="center">
              <v-col>
                <e-text-field
                  v-model="inviteEmail"
                  :name="$tc('views.camp.collaborators.email')"
                  vee-rules="email"
                  :error-messages="inviteEmailMessages"
                  :error-count="2"
                  single-line
                  aria-autocomplete="none"
                  :placeholder="$tc('views.camp.collaborators.email')"
                  @keyup="clearMessages"
                />
              </v-col>
              <v-col sm="12" md="3">
                <e-select
                  v-model="inviteRole"
                  :items="[
                    {
                      key: 'member',
                      translation: $tc('entity.camp.collaborators.member'),
                    },
                    {
                      key: 'manager',
                      translation: $tc('entity.camp.collaborators.manager'),
                    },
                    { key: 'guest', translation: $tc('entity.camp.collaborators.guest') },
                  ]"
                  item-value="key"
                  item-text="translation"
                  :my="0"
                  dense
                  vee-rules="required"
                />
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
import ContentCard from '@/components/layout/ContentCard.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import CollaboratorListItem from '@/components/collaborator/CollaboratorListItem.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import ETextField from '@/components/form/base/ETextField.vue'
import ESelect from '@/components/form/base/ESelect.vue'
import InactiveCollaboratorListItem from '@/components/collaborator/InactiveCollaboratorListItem.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import { transformViolations } from '@/helpers/serverError'

const DEFAULT_INVITE_ROLE = 'member'

export default {
  name: 'Collaborators',
  components: {
    ButtonAdd,
    CollaboratorListItem,
    ContentGroup,
    ContentCard,
    ETextField,
    ESelect,
    InactiveCollaboratorListItem,
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
::v-deep .v-skeleton-loader__list-item-avatar-two-line {
  height: 72px;
  padding-left: 0 !important;
  padding-right: 0 !important;
}

::v-deep .v-select__selections input {
  width: 20px;
}
</style>
