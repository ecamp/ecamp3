<!--
Displays collaborators of a single camp.
-->
<template>
  <div>
    Infos zu den Collaborators eines Lagers
    <table width="100%">
      <tr>
        <th>User</th>
        <th>Rolle</th>
        <th>Option</th>
      </tr>
      <tr v-for="collaborator in collaborators"
          :key="collaborator.id">
        <td>{{collaborator.user().username}}</td>
        <td>{{ collaborator.role }}</td>
        <td><button @click="removeCollaborator(collaborator)">Delete</button></td>
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
      messages: []
    }
  },
  computed: {
    campDetails () {
      return this.api.get(this.campUri)
    },
    collaborators () {
      return this.campDetails.camp_collaborations().items
    },
  },
  methods: {
    removeCollaborator (collaborator) {
      this.api.del(collaborator);
    }
  }
}
</script>
