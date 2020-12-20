<template>
  <v-expansion-panel>
    <v-expansion-panel-header hide-actions class="pa-0 pr-sm-2">
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
          style="flex: 1;"
          @click.stop
          @keyup.prevent>
          <api-text-field
            dense
            autofocus
            :auto-save="false"
            :uri="activityContent._meta.self"
            fieldname="instanceName"
            @finished="editInstanceName = false" />
        </div>
        <div v-else style="flex: 1;">
          <v-toolbar-title>
            {{ instanceOrContentTypeName }}
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
                {{ $tc('components.activity.activityContent.editName') }}
              </v-list-item-title>
            </v-list-item>
            <v-divider />
            <template v-if="dragDropEnabled">
              <v-list-item @click="() => $emit('move-up')">
                <v-list-item-icon>
                  <v-icon>mdi-arrow-up-drop-circle-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-title>
                  {{ $tc('components.activity.activityContent.moveUp') }}
                </v-list-item-title>
              </v-list-item>
              <v-list-item @click="() => $emit('move-down')">
                <v-list-item-icon>
                  <v-icon>mdi-arrow-down-drop-circle-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-title>
                  {{ $tc('components.activity.activityContent.moveDown') }}
                </v-list-item-title>
              </v-list-item>
              <v-divider />
            </template>
            <v-list-item @click="showDeleteActivityContentDialog">
              <v-list-item-icon>
                <v-icon>mdi-delete</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{ $tc('global.button.delete') }}
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
    Notes,
    DialogEntityDelete,
    ButtonDelete
  },
  props: {
    activityContent: { type: Object, required: true },
    dragDropEnabled: { type: Boolean, required: true }
  },
  data () {
    return {
      deleteDialogIsShown: false,
      editInstanceName: false,
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
    instanceOrContentTypeName () {
      if (this.activityContent.instanceName) {
        return this.activityContent.instanceName
      }
      return this.$tc(`activityContent.${camelCase(this.activityContent.contentTypeName)}.name`)
    }
  },
  mounted () {
    this.currentIcon = this.$tc(`activityContent.${camelCase(this.activityContent.contentTypeName)}.icon`)
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
</style>
