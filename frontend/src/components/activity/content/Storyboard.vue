<template>
  <card-content-node v-bind="$props">
    <v-container fluid>
      <v-row no-gutters class="text-subtitle-2">
        <v-col cols="2">
          {{ $tc('contentNode.storyboard.entity.section.fields.column1') }}
        </v-col>
        <v-col cols="7">
          {{ $tc('contentNode.storyboard.entity.section.fields.column2Html') }}
        </v-col>
        <v-col cols="2">
          {{ $tc('contentNode.storyboard.entity.section.fields.column3') }}
        </v-col>
        <v-col cols="1" />
      </v-row>

      <api-sortable
        v-slot="sortable"
        :disabled="layoutMode || disabled"
        :items="sections"
        @sort="updateSections"
      >
        <api-form :entity="contentNode">
          <v-row dense>
            <v-col cols="2">
              <api-text-field
                :fieldname="`data.sections[${sortable.itemKey}].column1`"
                :disabled="layoutMode || disabled"
                :filled="layoutMode"
              />
            </v-col>
            <v-col cols="7">
              <api-richtext
                :fieldname="`data.sections[${sortable.itemKey}].column2Html`"
                rows="4"
                :disabled="layoutMode || disabled"
                :filled="layoutMode"
              />
            </v-col>
            <v-col cols="2">
              <api-text-field
                :fieldname="`data.sections[${sortable.itemKey}].column3`"
                :disabled="layoutMode || disabled"
                :filled="layoutMode"
              />
            </v-col>
            <v-col cols="1">
              <v-container v-if="!layoutMode && !disabled" class="ma-0 pa-0">
                <v-row no-gutters>
                  <v-col cols="6">
                    <div class="section-buttons">
                      <dialog-remove-section
                        @submit="sortable.on.delete(sortable.itemKey)"
                      >
                        <template #activator="{ on }">
                          <v-btn
                            icon
                            x-small
                            color="error"
                            :disabled="isLastSection"
                            v-on="on"
                          >
                            <v-icon>mdi-delete</v-icon>
                          </v-btn>
                        </template>
                      </dialog-remove-section>
                    </div>
                    <v-btn
                      icon
                      x-small
                      class="drag-and-drop-handle"
                      :disabled="isLastSection"
                    >
                      <v-icon>mdi-drag-horizontal-variant</v-icon>
                    </v-btn>
                  </v-col>
                  <v-col cols="6">
                    <div class="section-buttons">
                      <v-btn
                        icon
                        x-small
                        :disabled="isLastSection"
                        @click="sortable.on.moveUp(sortable.itemKey)"
                      >
                        <v-icon>mdi-arrow-up-bold</v-icon>
                      </v-btn>

                      <v-btn
                        icon
                        x-small
                        :disabled="isLastSection"
                        @click="sortable.on.moveDown(sortable.itemKey)"
                      >
                        <v-icon>mdi-arrow-down-bold</v-icon>
                      </v-btn>
                    </div>
                  </v-col>
                </v-row>
              </v-container>
            </v-col>
          </v-row>
        </api-form>
      </api-sortable>

      <!-- add at end position -->
      <v-row no-gutters justify="center">
        <v-col cols="1">
          <v-btn
            v-if="!layoutMode && !disabled"
            icon
            small
            class="button-add"
            color="success"
            :loading="isAdding"
            @click="addSection"
          >
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </card-content-node>
</template>

<script>
import ApiForm from '@/components/form/api/ApiForm.vue'
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import ApiSortable from '@/components/form/api/ApiSortable.vue'

import DialogRemoveSection from './StoryboardDialogRemoveSection.vue'

import { v4 as uuidv4 } from 'uuid'
import { errorToMultiLineToast } from '@/components/toast/toasts'

export default {
  name: 'Storyboard',
  components: {
    CardContentNode,
    ApiForm,
    ApiSortable,
    DialogRemoveSection,
  },
  mixins: [contentNodeMixin],
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return {
      isAdding: false,
    }
  },
  computed: {
    sections() {
      const sections = this.api.get(this.contentNode).data.sections

      // workaround because our API return an empty array instead of empty object, if there is no section
      // this could be removed later, when no empty sections objects are in the DB anymore
      if (Array.isArray(sections) && sections.length === 0) {
        return {}
      }

      return sections
    },
    isLastSection() {
      return Object.keys(this.sections).length === 1
    },
  },
  methods: {
    async addSection() {
      this.isAdding = true

      const sectionId = uuidv4()
      try {
        // TODO: consider adding item to ApiSortable eagerly (should be easy, now that uuid is generated locally)
        await this.contentNode.$patch({
          data: {
            sections: {
              [sectionId]: {
                column1: '',
                column2Html: '',
                column3: '',
                position: Object.keys(this.sections).length + 1,
              },
            },
          },
        })
      } catch (error) {
        this.$toast.error(errorToMultiLineToast(error))
      }

      this.isAdding = false
    },

    async updateSections(payload) {
      try {
        await this.contentNode.$patch({
          data: {
            sections: payload,
          },
        })
      } catch (error) {
        this.$toast.error(errorToMultiLineToast(error))
      }
    },
  },
}
</script>

<style scoped>
.section-buttons {
  width: 40px;
}

.row-inter {
  height: 4px;
  transition: 0s height;
  transition-duration: 0.5s;
}
.row-inter:hover {
  height: 30px;
  background-color: #eeeeee;
  transition-delay: 0.3s;
}

.row-inter .button-add {
  opacity: 0;
  height: 0;
  transition: 0s height, opacity;
  transition-duration: 0.5s;
}

.row-inter:hover .button-add {
  opacity: 1;
  height: 30px;
  transition-delay: 0.3s;
}
</style>
