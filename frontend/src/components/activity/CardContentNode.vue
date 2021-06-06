<template>
  <v-card :elevation="draggable ? 4 : 0" :class="{ 'mx-2 my-2': draggable }">
    <v-card-title hide-actions class="pa-0 pr-sm-2">
      <v-toolbar dense flat>
        <v-menu v-if="!disabled"
                bottom
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
        <v-icon v-else>
          {{ currentIcon }}
        </v-icon>

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

        <v-menu v-if="!layoutMode && !disabled"
                bottom
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
          </v-list>
        </v-menu>
        <dialog-entity-delete
          v-else-if="!disabled"
          :entity="contentNode">
          <template #activator="{ on }">
            <v-btn icon
                   small
                   color="error"
                   class="float-right"
                   v-on="on">
              <v-icon>mdi-trash-can-outline</v-icon>
            </v-btn>
          </template>
        </dialog-entity-delete>
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <slot />
    </v-card-text>
  </v-card>
</template>

<script>

import camelCase from 'lodash/camelCase'
import ApiTextField from '../form/api/ApiTextField.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'

export default {
  name: 'CardContentNode',
  components: {
    ApiTextField, DialogEntityDelete
  },
  props: {
    contentNode: { type: Object, required: true },
    layoutMode: { type: Boolean, required: true },
    draggable: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false }
  },
  data () {
    return {
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
      if (this.disabled) {
        return
      }
      this.editInstanceName = !this.editInstanceName
    }
  }
}
</script>

<style scoped>
</style>
