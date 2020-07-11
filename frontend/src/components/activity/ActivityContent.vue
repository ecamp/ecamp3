<template>
  <v-expansion-panel>
    <v-expansion-panel-header class="pa-0 pr-sm-2" expand-icon="">
      <v-toolbar dense flat>
        <v-icon class="mr-2 drag-handle">
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

        <v-icon class="drag-handle ml-4 mr-2 hidden-xs-only">
          mdi-drag-horizontal-variant
        </v-icon>

        <v-menu bottom
                left
                offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item @click="toggleEditInstanceName">
              <v-list-item-icon>
                <v-icon>mdi-pencil</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                Edit Name
              </v-list-item-title>
            </v-list-item>
            <v-divider />
            <v-list-item @click="() => $emit('move-up')">
              <v-list-item-icon>
                <v-icon>mdi-arrow-up-drop-circle-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                Move up
              </v-list-item-title>
            </v-list-item>
            <v-list-item @click="() => $emit('move-down')">
              <v-list-item-icon>
                <v-icon>mdi-arrow-down-drop-circle-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                Move down
              </v-list-item-title>
            </v-list-item>
            <v-divider />
            <v-list-item @click="showDeleteActivityContentDialog">
              <v-list-item-icon>
                <v-icon>mdi-delete</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                Delete
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-toolbar>
      <dialog-entity-delete ref="deleteActivityContentDialog" :entity="activityContent" />
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
      deleteDialogIsShown: false,
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
    toggleEditInstanceName (e) {
      this.editInstanceName = !this.editInstanceName
    },
    showDeleteActivityContentDialog (e) {
      this.$refs.deleteActivityContentDialog.showDialog = true
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
