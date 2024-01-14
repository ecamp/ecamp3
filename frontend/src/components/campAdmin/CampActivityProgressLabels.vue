<template>
  <content-group>
    <slot name="title">
      <div class="ec-content-group__title py-1 subtitle-1">
        {{ $tc('components.campAdmin.campActivityProgressLabels.title') }}
        <dialog-activity-progress-label-create v-if="!disabled" :camp="camp()">
          <template #activator="{ on }">
            <button-add
              color="secondary"
              text
              :hide-label="$vuetify.display.xsOnly"
              class="my-n2"
              v-on="on"
            >
              {{ $tc('components.campAdmin.campActivityProgressLabels.create') }}
            </button-add>
          </template>
        </dialog-activity-progress-label-create>
      </div>
    </slot>
    <v-skeleton-loader v-if="loading" type="article" />
    <v-list>
      <v-list-item
        v-for="(progressLabel, idx) in progressLabels"
        :key="progressLabel._meta.self"
        class="px-0"
      >
        <v-list-item-avatar>
          <v-avatar color="grey lighten-2" size="32">{{ idx + 1 }}</v-avatar>
        </v-list-item-avatar>
        <v-list-item-content>
          <v-list-item-title>
            {{ progressLabel.title }}
          </v-list-item-title>
        </v-list-item-content>

        <v-list-item-action v-if="!disabled" style="display: inline">
          <v-item-group>
            <dialog-activity-progress-label-edit :progress-label="progressLabel">
              <template #activator="{ on }">
                <button-edit class="mr-1" v-on="on" />
              </template>
            </dialog-activity-progress-label-edit>
          </v-item-group>
        </v-list-item-action>

        <v-menu v-if="!disabled" offset-y>
          <template #activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item @click="moveUp(progressLabel)">
              <v-list-item-icon>
                <v-icon>mdi-arrow-up-bold</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{ $tc('components.campAdmin.campActivityProgressLabels.moveUp') }}
              </v-list-item-title>
            </v-list-item>
            <v-list-item @click="moveDown(progressLabel)">
              <v-list-item-icon>
                <v-icon>mdi-arrow-down-bold</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{ $tc('components.campAdmin.campActivityProgressLabels.moveDown') }}
              </v-list-item-title>
            </v-list-item>

            <v-divider />

            <dialog-entity-delete
              :entity="progressLabel"
              :error-handler="deleteErrorHandler"
            >
              <template #activator="{ on }">
                <v-list-item v-on="on">
                  <v-list-item-icon>
                    <v-icon>mdi-delete</v-icon>
                  </v-list-item-icon>
                  <v-list-item-title>
                    {{ $tc('global.button.delete') }}
                  </v-list-item-title>
                </v-list-item>
              </template>
              {{ $tc('components.campAdmin.campActivityProgressLabels.deleteWarning') }}
              <ul>
                <li>
                  {{ progressLabel.title }}
                </li>
              </ul>
            </dialog-entity-delete>
          </v-list>
        </v-menu>
      </v-list-item>
    </v-list>
  </content-group>
</template>

<script>
import { sortBy } from 'lodash'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import DialogActivityProgressLabelCreate from './DialogActivityProgressLabelCreate.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'

export default {
  name: 'CampActivityProgressLabels',
  components: {
    ContentGroup,
    DialogActivityProgressLabelCreate,
    DialogEntityDelete,
  },
  props: {
    camp: { type: Function, required: true },
    disabled: { type: Boolean, default: false },
  },
  computed: {
    loading() {
      if (this.camp()._meta.loading) return true

      const progressLabels = this.camp().progressLabels()
      if (progressLabels._meta.loading) return true
      if (progressLabels.items.some((label) => label._meta.loading)) return true

      return false
    },
    progressLabels() {
      if (!this.loading) {
        const progressLabels = this.camp().progressLabels()
        return sortBy(progressLabels.allItems, (label) => label.position)
      }
      return []
    },
  },
  methods: {
    moveUp(progressLabel) {
      progressLabel
        .$patch({ position: progressLabel.position - 1 })
        .then(() => this.camp().progressLabels().$reload())
    },
    moveDown(progressLabel) {
      progressLabel
        .$patch({ position: progressLabel.position + 1 })
        .then(() => this.camp().progressLabels().$reload())
    },
    deleteErrorHandler(e) {
      if (e?.response?.status === 422 /* Validation Error */) {
        return this.$tc('components.campAdmin.campActivityProgressLabels.deleteError')
      }

      return null
    },
  },
}
</script>

<style scoped></style>
