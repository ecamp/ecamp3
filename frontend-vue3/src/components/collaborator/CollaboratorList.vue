<template>
  <v-list class="mx-n2">
    <transition-group name="list">
      <div v-for="collaborator in sortedCollaborators" :key="collaborator._meta.self">
        <CollaboratorEdit
          v-if="isManager || isOwnCollaborationMap[collaborator._meta.self]"
          :collaborator="collaborator"
          :inactive="inactive"
        />
        <CollaboratorListItem
          v-else
          :key="collaborator._meta.self"
          :collaborator="collaborator"
          :inactive="inactive"
        />
      </div>
    </transition-group>
  </v-list>
</template>

<script>
import CollaboratorEdit from '@/components/collaborator/CollaboratorEdit.vue'
import CollaboratorListItem from '@/components/collaborator/CollaboratorListItem.vue'
import { sortBy } from 'lodash-es'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'
import isOwnCampCollaboration from './isOwnCampCollaboration.js'

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
          String(ROLE_ORDER.indexOf(c.role)).padStart(3, '0') +
          campCollaborationDisplayName(c, this.$tc.bind(this)).toLowerCase()
      )
    },

    isOwnCollaborationMap() {
      const result = {}
      this.collaborators.forEach((collaborator) => {
        result[collaborator._meta.self] = isOwnCampCollaboration(
          collaborator,
          this.$store.state.auth
        )
      })
      return result
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
