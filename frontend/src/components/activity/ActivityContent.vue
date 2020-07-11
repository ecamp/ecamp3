<template>
  <v-expansion-panel>
    <v-expansion-panel-header class="pl-0 pt-0 pb-0">
      <v-toolbar dense flat>
        <v-icon class="mr-2">
          {{ mdiIcon }}
        </v-icon>
        <div
          v-if="editInstanceName"
          style="flex: 1;"
          @click.stop
          @keyup.prevent>
          <api-text-field
            dense
            autofocus
            :uri="activityContent._meta.self"
            fieldname="instanceName" />
        </div>
        <div v-else style="flex: 1;">
          <v-toolbar-title>
            {{ instanceName }}
          </v-toolbar-title>
        </div>

        <v-btn class="float-right" icon @click.stop="toggleEditInstanceName">
          <v-icon v-if="editInstanceName" color="success">mdi-pencil</v-icon>
          <v-icon v-else>mdi-pencil</v-icon>
        </v-btn>
        <dialog-entity-delete :entity="activityContent">
          <template v-slot:activator="{ on }">
            <v-btn
              class="float-right delete-button"
              icon
              v-on="on">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </template>
        </dialog-entity-delete>
      </v-toolbar>
    </v-expansion-panel-header>
    <v-expansion-panel-content>
      <component :is="activityContent.contentTypeName" :activity-content="activityContent" />
    </v-expansion-panel-content>
  </v-expansion-panel>
</template>

<script>

import SafetyConcept from '@/components/activity/content/SafetyConcept'
import Storycontext from '@/components/activity/content/Storycontext'
import Storyboard from '@/components/activity/content/Storyboard'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete'
import ButtonDelete from '@/components/buttons/ButtonDelete'
import camelCase from 'lodash/camelCase'
import ApiTextField from '../form/api/ApiTextField'

export default {
  name: 'ActivityContent',
  components: {
    ApiTextField,
    SafetyConcept,
    Storycontext,
    Storyboard,
    DialogEntityDelete,
    ButtonDelete
  },
  props: {
    activityContent: { type: Object, required: true }
  },
  data () {
    return {
      isDeleting: false,
      editInstanceName: false
    }
  },
  computed: {
    instanceName () {
      if (this.activityContent.instanceName) {
        return this.activityContent.instanceName
      }
      return this.$t(`activityContent.${camelCase(this.activityContent.contentTypeName)}.name`)
    },
    mdiIcon () {
      return this.$t(`activityContent.${camelCase(this.activityContent.contentTypeName)}.icon`)
    }
  },
  methods: {
    async removeActivityContent () {
      this.api.del(this.activityContent)
    },
    toggleEditInstanceName (e) {
      this.editInstanceName = !this.editInstanceName
    }
  }
}
</script>

<style scoped>
  .delete-button:hover,
  .delete-button:focus {
    color: red;
  }
</style>
