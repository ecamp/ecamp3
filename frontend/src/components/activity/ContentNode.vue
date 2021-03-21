<template>
  <v-card outlined class="mt-3">
    <v-card-title hide-actions class="pa-0 pr-sm-2">
      <v-toolbar dense flat>
        <v-menu bottom
                right
                offset-y>
          <template #activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>
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
            :uri="contentNode._meta.self"
            fieldname="instanceName"
            @finished="editInstanceName = false" />
        </div>
        <div v-else style="flex: 1;">
          <v-toolbar-title>
            {{ instanceOrContentTypeName }}
          </v-toolbar-title>
        </div>

        <v-menu bottom
                left
                offset-y>
          <template #activator="{ on, attrs }">
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
                {{ $tc('components.activity.contentNode.editName') }}
              </v-list-item-title>
            </v-list-item>
            <v-divider />
            <v-list-item @click="showDeleteContentNodeDialog">
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
      <dialog-entity-delete ref="deleteContentNodeDialog" :entity="contentNode" />
    </v-card-title>
    <v-card-text>
      <component :is="contentNode.contentTypeName" :content-node="contentNode" />
    </v-card-text>
  </v-card>
</template>

<script>

import SafetyConcept from '@/components/activity/content/SafetyConcept'
import Storycontext from '@/components/activity/content/Storycontext'
import Storyboard from '@/components/activity/content/Storyboard'
import Notes from '@/components/activity/content/Notes'
import Material from '@/components/activity/content/Material'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete'
import ButtonDelete from '@/components/buttons/ButtonDelete'
import camelCase from 'lodash/camelCase'
import ApiTextField from '../form/api/ApiTextField'
import LAThematicArea from '@/components/activity/content/LAThematicArea'

export default {
  name: 'ContentNode',
  components: {
    ApiTextField,
    SafetyConcept,
    Storycontext,
    Storyboard,
    LAThematicArea,
    Notes,
    Material,
    DialogEntityDelete,
    ButtonDelete
  },
  props: {
    contentNode: { type: Object, required: true }
  },
  data () {
    return {
      deleteDialogIsShown: false,
      editInstanceName: false,
      allowedIcons: [
        [
          'mdi-book-open-variant'
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
      if (this.contentNode.instanceName) {
        return this.contentNode.instanceName
      }
      return this.$tc(`contentNode.${camelCase(this.contentNode.contentTypeName)}.name`)
    }
  },
  mounted () {
    this.currentIcon = this.$tc(`contentNode.${camelCase(this.contentNode.contentTypeName)}.icon`)
  },
  methods: {
    toggleEditInstanceName (e) {
      this.editInstanceName = !this.editInstanceName
    },
    showDeleteContentNodeDialog (e) {
      this.$refs.deleteContentNodeDialog.showDialog = true
    }
  }
}
</script>

<style scoped>
</style>
