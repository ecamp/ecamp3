<!--
Displays collaborators of a single camp.
-->
<template>
  <v-card>
    <v-toolbar dense color="blue-grey lighten-5">
      <v-icon left>
        mdi-account-group
      </v-icon>
      <v-toolbar-title>Mitarbeiter</v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <v-simple-table width="100%">
        <thead>
          <tr>
            <th
              width="50%">
              User
            </th>
            <th
              width="25%">
              Rolle
            </th>
            <th
              width="25%">
              Option
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="collaborator in establishedCollaborators"
            :key="collaborator.id">
            <td>
              <v-skeleton-loader v-if="collaborator.user().loaded" type="text" />
              {{ collaborator.user().username }}
            </td>
            <td>
              <api-single-select
                :value="collaborator.role"
                :uri="collaborator._meta.self"
                fieldname="role"
                required
                dense />
            </td>
            <td
              style="white-space: nowrap">
              <v-btn
                small
                color="warning"
                @click="api.del(collaborator)">
                <v-icon
                  small
                  left>
                  mdi-close
                </v-icon>
                delete
              </v-btn>
            </td>
          </tr>
        </tbody>
      </v-simple-table>

      <v-divider />

      <div v-if="requestedCollaborators.length > 0">
        <h3 class="mt-4">
          Offene Anfragen
        </h3>
        <v-simple-table width="100%">
          <tr>
          <tr>
            <th
              width="50%">
              User
            </th>
            <th
              width="25%">
              Rolle
            </th>
            <th
              width="25%">
              Option
            </th>
          </tr>
          <tr
            v-for="collaborator in requestedCollaborators"
            :key="collaborator.id">
            <td>{{ collaborator.user().username }}</td>
            <td>
              <api-single-select
                :value="collaborator.role"
                :uri="collaborator._meta.self"
                fieldname="role"
                required
                dense />
            </td>
            <td
              style="white-space: nowrap">
              <v-btn
                small
                color="success"
                @click="changeStatus(collaborator, 'established')">
                <v-icon
                  small
                  left>
                  mdi-check
                </v-icon>
                accept
              </v-btn>
              <v-btn
                small
                color="warning"
                @click="changeStatus(collaborator, 'unrelated')">
                <v-icon
                  small
                  left>
                  mdi-close
                </v-icon>
                deny
              </v-btn>
            </td>
          </tr>
        </v-simple-table>
        <v-divider />
      </div>

      <div v-if="invitedCollaborators.length > 0">
        <h3 class="mt-4">
          Offene Einladungen
        </h3>
        <v-simple-table width="100%">
          <thead>
            <tr>
            <tr>
              <th
                width="50%">
                User
              </th>
              <th
                width="25%">
                Rolle
              </th>
              <th
                width="25%">
                Option
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="collaborator in invitedCollaborators"
              :key="collaborator.id">
              <td>{{ collaborator.user().username }}</td>
              <td>
                <api-single-select
                  :value="collaborator.role"
                  :uri="collaborator._meta.self"
                  fieldname="role"
                  required
                  dense />
              </td>
              <td
                style="white-space: nowrap">
                <v-btn
                  small
                  color="warning"
                  @click="api.del(collaborator)">
                  <v-icon
                    small
                    left>
                    mdi-close
                  </v-icon>
                  delete
                </v-btn>
              </td>
            </tr>
          </tbody>
        </v-simple-table>
        <v-divider />
      </div>

      <h3 class="mt-4">
        Einladen
      </h3>

      <v-text-field
        v-model="search"
        hide-details
        prepend-icon="mdi-account-search"
        single-line
        placeholder="Suchen" />

      <v-simple-table
        width="100%"
        class="mt-4">
        <tr
          v-for="result in searchResults"
          :key="result.id">
          <td>{{ result.username }}</td>
          <td
            style="white-space: nowrap">
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
          </td>
        </tr>
      </v-simple-table>
    </v-card-text>
  </v-card>
</template>
<script>
export default {
  name: 'Collaborators',
  components: {
    ApiSingleSelect: () => import('@/components/form/ApiSingleSelect.vue')
  },
  props: {
    camp: { type: Object, required: true }
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
      return this.camp.camp_collaborations().items.filter(c => !c._meta.deleting)
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
        camp_id: this.camp.id,
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
