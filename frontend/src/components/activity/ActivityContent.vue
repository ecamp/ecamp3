<template>
  <v-expansion-panel>
    <v-expansion-panel-header class="pa-0 pr-sm-2" expand-icon="">
      <v-toolbar dense flat>
        <v-menu bottom
                right
                offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon class="drag-handle">
                {{ currentIcon }}
              </v-icon>
            </v-btn>
          </template>
          <v-container class="grey lighten-5">
            <v-row v-for="(row, idx) in allowedIcons" :key="idx" no-gutters>
              <v-col v-for="(col, jdx) in row" :key="jdx">
                <v-btn icon
                       tile
                       :outlined="currentIcon === col"
                       class="ma-1"
                       @click="currentIcon = col">
                  <v-icon>{{ col }}</v-icon>
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-menu>

        <div
          v-if="editInstanceName"
          style="display: flex; flex: 1; align-items: center"
          @click.stop>
          <div
            style="flex: 1"
            @keyup.prevent>
            <e-text-field
              v-model="instanceName"
              :placeholder="contentTypeName"
              :filled="false"
              :loading="editInstanceNameSaving"
              autofocus />
          </div>
          <v-btn
            color="success"
            style="flex: 0 0 30px"
            class="ml-2"
            @click.stop="saveEditInstanceName">
            <v-icon>mdi-check</v-icon>
          </v-btn>
          <v-btn
            color="error"
            style="flex: 0 0 30px"
            class="mx-2"
            @click.stop="cancelEditInstanceName">
            <v-icon>mdi-window-close</v-icon>
          </v-btn>
        </div>
        <div v-else style="flex: 1;">
          <v-toolbar-title>
            {{ instanceNameDisp }}
          </v-toolbar-title>
        </div>

        <v-icon v-if="dragDropEnabled" class="drag-handle ml-4 mr-2 hidden-xs-only">
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
import Notes from '@/components/activity/content/Notes'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete'
import camelCase from 'lodash/camelCase'
import ETextField from '../form/base/ETextField'

export default {
  name: 'ActivityContent',
  components: {
    ETextField,
    SafetyConcept,
    Storycontext,
    Storyboard,
    Notes,
    DialogEntityDelete
  },
  props: {
    activityContent: { type: Object, required: true },
    dragDropEnabled: { type: Boolean, required: true }
  },
  data () {
    return {
      deleteDialogIsShown: false,
      instanceName: '',
      editInstanceName: false,
      editInstanceNameSaving: false,
      allowedIcons: [
        [
          'mdi-book-open-page-variant'
        ],
        [
          'mdi-script-text-outline',
          'mdi-timeline-text-outline',
          'mdi-weather-sunny',
          'mdi-weather-lightning-rainy',
          'mdi-gender-female',
          'mdi-gender-male'
        ],
        [
          'mdi-security',
          'mdi-hospital-box-outline'
        ]
      ],
      currentIcon: ''
    }
  },
  computed: {
    contentTypeName () {
      return this.$tc(`activityContent.${camelCase(this.activityContent.contentTypeName)}.name`)
    },
    instanceNameDisp () {
      if (this.activityContent.instanceName) {
        return this.activityContent.instanceName
      }
      return this.contentTypeName
    }
  },
  mounted () {
    this.currentIcon = this.$tc(`activityContent.${camelCase(this.activityContent.contentTypeName)}.icon`)
  },
  methods: {
    toggleEditInstanceName () {
      this.editInstanceName = !this.editInstanceName
      this.instanceName = this.activityContent.instanceName
    },
    showDeleteActivityContentDialog () {
      this.$refs.deleteActivityContentDialog.showDialog = true
    },
    saveEditInstanceName () {
      this.editInstanceNameSaving = true
      this.api.patch(this.activityContent._meta.self, { instanceName: this.instanceName }).then(() => {
        this.editInstanceNameSaving = false
        this.editInstanceName = false
      }, () => {
        this.editInstanceNameSaving = false
      })
    },
    cancelEditInstanceName () {
      this.editInstanceName = false
    }
  }
}
</script>
