<template>
  <content-group
    :title="$tc('components.campAdmin.campActivityProgressLabels.title')"
    icon="mdi-eye-check"
  >
    <template #title-actions>
      <DialogActivityProgressLabelCreate v-if="!disabled" :camp="camp">
        <template #activator="{ on }">
          <ButtonAdd
            color="secondary"
            text
            :hide-label="$vuetify.breakpoint.xsOnly"
            class="my-n2"
            v-on="on"
          >
            {{ $tc('components.campAdmin.campActivityProgressLabels.create') }}
          </ButtonAdd>
        </template>
      </DialogActivityProgressLabelCreate>
    </template>
    <v-skeleton-loader
      v-if="camp.progressLabels()._meta.loading"
      type="list-item@3"
      class="mx-n4"
    />
    <v-list class="mx-n2">
      <template v-if="disabled">
        <v-list-item
          v-for="(progressLabel, idx) in progressLabels"
          :key="progressLabel._meta.self"
          class="px-2 rounded"
        >
          <v-avatar color="rgba(0,0,0,0.12)" class="mr-2" size="32">{{
            parseInt(idx) + 1
          }}</v-avatar>
          <v-list-item-content>
            <v-list-item-title>
              {{ progressLabel.title }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-else-if="!reorder">
        <DialogActivityProgressLabelEdit
          v-for="(progressLabel, idx) in progressLabels"
          :key="progressLabel._meta.self"
          :progress-label="progressLabel"
        >
          <template #activator="{ on }">
            <v-list-item class="px-2 rounded" v-on="on">
              <v-avatar color="rgba(0,0,0,0.12)" class="mr-2" size="32">{{
                parseInt(idx) + 1
              }}</v-avatar>
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
      </template>
      <template v-else>
        <api-sortable
          v-slot="{ itemPosition, item, on }"
          :endpoint="camp.progressLabels()"
        >
          <v-list-item class="px-2 rounded drag-and-drop-handle" v-on="on">
            <v-avatar color="rgba(0,0,0,0.12)" class="mr-2" size="32">{{
              itemPosition + 1
            }}</v-avatar>
            <v-list-item-content>
              <v-list-item-title>
                {{ item.title }}
              </v-list-item-title>
            </v-list-item-content>

            <v-list-item-action style="display: inline">
              <v-btn text plain icon class="my-n1 pointer-events-none">
                <v-icon>mdi-drag</v-icon>
              </v-btn>
            </v-list-item-action>
          </v-list-item>
        </api-sortable>
      </template>
    </v-list>
    <v-btn v-if="!disabled" text block @click="reorder = !reorder">
      <v-icon left>{{ reorder ? 'mdi-close' : 'mdi-sort' }}</v-icon>
      {{
        reorder
          ? $tc('components.campAdmin.campActivityProgressLabels.exit')
          : $tc('components.campAdmin.campActivityProgressLabels.reorder')
      }}
    </v-btn>
  </content-group>
</template>

<script>
import { sortBy } from 'lodash'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import DialogActivityProgressLabelCreate from './DialogActivityProgressLabelCreate.vue'
import DialogActivityProgressLabelEdit from '@/components/campAdmin/DialogActivityProgressLabelEdit.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import ApiSortable from '@/components/form/api/ApiSortable.vue'
export default {
  name: 'CampActivityProgressLabels',
  components: {
    ApiSortable,
    ButtonAdd,
    ButtonEdit,
    ContentGroup,
    DialogActivityProgressLabelCreate,
    DialogActivityProgressLabelEdit,
  },
  props: {
    camp: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
  },
  data: () => ({
    reorder: false,
  }),
  computed: {
    progressLabels() {
      return sortBy(this.camp.progressLabels().allItems, (label) => label.position)
    },
  },
}
</script>
