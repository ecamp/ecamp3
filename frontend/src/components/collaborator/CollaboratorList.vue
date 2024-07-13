<template>
  <v-list class="mx-n2">
    <transition-group v-if="isManager" name="list">
      <div v-for="collaborator in sortedCollaborators" :key="collaborator._meta.self">
        <CollaboratorEdit :collaborator="collaborator" :inactive="inactive" />
      </div>
    </transition-group>
    <template v-else>
      <CollaboratorListItem
        v-for="collaborator in sortedCollaborators"
        :key="collaborator._meta.self"
        :collaborator="collaborator"
        :inactive="inactive"
      />
    </template>
  </v-list>
</template>

<script>
import CollaboratorEdit from '@/components/collaborator/CollaboratorEdit.vue'
import CollaboratorListItem from '@/components/collaborator/CollaboratorListItem.vue'
import { sortBy } from 'lodash'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'

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
  },
  computed: {
    sortedCollaborators() {
      return sortBy(
        [...this.collaborators],
        (c) =>
          ROLE_ORDER.indexOf(c.role) +
          campCollaborationDisplayName(c, this.$tc.bind(this)).toLowerCase()
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
