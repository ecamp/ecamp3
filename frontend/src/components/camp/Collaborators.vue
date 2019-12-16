<!--
Displays collaborators of a single camp.
-->
<template>
  <div>
    <h3>Mitarbeiter</h3>
    <table width="100%">
      <tr>
        <th>User</th>
        <th>Rolle</th>
        <th>Option</th>
      </tr>
      <tr v-for="collaborator in establishedCollaborators" :key="collaborator.id">
        <td>{{ collaborator.user().username }}</td>
        <td>{{ collaborator.role }}</td>
        <td width="150">
          <div class="btn-group" role="group">
            <button class="btn btn-sm btn-primary">
              <i class="zmdi zmdi-edit"></i> edit
            </button>
            <button @click="api.del(collaborator)" class="btn btn-sm btn-danger">
              <i class="zmdi zmdi-close"></i> delete
            </button>
          </div>
        </td>
      </tr>
    </table>
    <hr />

    <div v-if="requestedCollaborators.length > 0">
      <h3>Offene Anfragen</h3>
      <table width="100%">
        <tr>
          <th>User</th>
          <th>Rolle</th>
          <th>Option</th>
        </tr>
        <tr v-for="collaborator in requestedCollaborators" :key="collaborator.id">
          <td>{{ collaborator.user().username }}</td>
          <td>{{ collaborator.role }}</td>
          <td width="150">
            <div class="btn-group" role="group">
              <button @click="changeStatus(collaborator, 'established')" class="btn btn-sm btn-success">
                <i class="zmdi zmdi-check"></i> accept
              </button>
              <button @click="changeStatus(collaborator, 'unrelated')" class="btn btn-sm btn-danger">
                <i class="zmdi zmdi-close"></i> deny
              </button>
            </div>
          </td>
        </tr>
      </table>
      <hr />
    </div>

    <div v-if="invitedCollaborators.length > 0">
      <h3>Offene Einladungen</h3>
      <table width="100%">
        <tr>
          <th>User</th>
          <th>Rolle</th>
          <th>Option</th>
        </tr>
        <tr v-for="collaborator in invitedCollaborators" :key="collaborator.id">
          <td>{{ collaborator.user().username }}</td>
          <td>{{ collaborator.role }}</td>
          <td width="150">
            <div class="btn-group" role="group">
              <button class="btn btn-sm btn-primary">
                <i class="zmdi zmdi-edit"></i> edit
              </button>
              <button @click="api.del(collaborator)" class="btn btn-sm btn-danger">
                <i class="zmdi zmdi-close"></i> delete
              </button>
            </div>
          </td>
        </tr>
      </table>
      <hr />
    </div>

    <h3>Einladen</h3>
    <table width="100%" cellpadding="3">
      <tr>
        <td colspan="2">
          <input type="text" class="form-control form-control-sm" placeholder="Suchen" v-model="search">
        </td>
      </tr>

      <tr v-for="result in searchResults" :key="result.id">
        <td>{{ result.username }}</td>
        <td width="200">
          <button @click="invite(result, 'member')" class="btn btn-sm btn-success" style="margin-right: 4px">
            <i class="zmdi zmdi-plus-circle-o"></i> member
          </button>
          <button @click="invite(result, 'manager')" class="btn btn-sm btn-success">
            <i class="zmdi zmdi-plus-circle-o"></i> manager
          </button>
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
export default {
  name: 'Collaborators',
  props: {
    campUri: { type: String, required: true }
  },
  data () {
    return {
      editing: false,
      messages: [],
      search: ''
    }
  },
  computed: {
    campDetails () {
      return this.api.get(this.campUri)
    },
    collaborators () {
      return this.campDetails.camp_collaborations().items.filter(c => !c._meta.deleting)
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
        let filterUsers = this.collaborators.filter(
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
        camp_id: this.campDetails.id,
        user_id: user.id,
        role: role
      })
    }
  }
}
</script>
