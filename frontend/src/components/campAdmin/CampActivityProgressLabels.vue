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
              :hide-label="$vuetify.breakpoint.xsOnly"
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
    <v-list class="mx-n2">
      <DialogActivityProgressLabelEdit
        v-for="(progressLabel, idx) in progressLabels"
        :key="progressLabel._meta.self"
        :progress-label="progressLabel"
      >
        <template #activator="{ on }">
          <v-list-item class="px-2 rounded" v-on="on">
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
                <ButtonEdit color="primary--text" text class="my-n1 v-btn--has-bg" />
              </v-item-group>
            </v-list-item-action>
          </v-list-item>
        </template>
      </DialogActivityProgressLabelEdit>
    </v-list>
  </content-group>
</template>

<script>
import { sortBy } from 'lodash'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import DialogActivityProgressLabelCreate from './DialogActivityProgressLabelCreate.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import DialogActivityProgressLabelEdit from '@/components/campAdmin/DialogActivityProgressLabelEdit.vue'

export default {
  name: 'CampActivityProgressLabels',
  components: {
    DialogActivityProgressLabelEdit,
    ButtonEdit,
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
