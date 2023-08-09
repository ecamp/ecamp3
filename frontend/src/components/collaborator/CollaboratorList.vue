<template>
  <v-list class="mx-n2">
    <transition-group v-if="isManager" name="list">
      <div v-for="collaborator in sortedCollaborators" :key="collaborator._meta.self">
        <CollaboratorEdit
          :activities="groupedActivities[collaborator._meta.self]"
          :collaborator="collaborator"
          :inactive="inactive"
        />
      </div>
    </transition-group>
    <template v-else>
      <CollaboratorListItem
        v-for="collaborator in sortedCollaborators"
        :key="collaborator._meta.self"
        :activities="groupedActivities[collaborator._meta.self]"
        :collaborator="collaborator"
        :inactive="inactive"
      />
    </template>
  </v-list>
</template>

<script>
import CollaboratorEdit from '@/components/collaborator/CollaboratorEdit.vue'
import CollaboratorListItem from '@/components/collaborator/CollaboratorListItem.vue'

const ROLE_ORDER = ['manager', 'member', 'guest']

export default {
  name: 'CollaboratorList',
  components: {
    CollaboratorListItem,
    CollaboratorEdit,
  },
  props: {
    collaborators: { type: Array, required: true },
    isManager: { type: Boolean, default: false },
    inactive: { type: Boolean, default: false },
    groupedActivities: { type: Object, required: false, default: () => ({}) },
  },
  computed: {
    sortedCollaborators() {
      return [...this.collaborators].sort(
        (a, b) => ROLE_ORDER.indexOf(a.role) - ROLE_ORDER.indexOf(b.role)
      )
    },
  },
}
</script>

<style scoped>
/* apply transition to moving elements */
.list-move {
  transition: transform 0.5s ease;
}

/* ensure leaving items are taken out of layout flow so that moving
   animations can be calculated correctly. */
.list-leave-active {
  position: absolute;
}
</style>
