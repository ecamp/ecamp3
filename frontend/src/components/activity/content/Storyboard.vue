<template>
  <card-content-node v-bind="$props">
    <v-container fluid>
      <v-row no-gutters class="text-subtitle-2">
        <v-col cols="2">
          {{ $tc('contentNode.storyboard.entity.section.fields.column1') }}
        </v-col>
        <v-col cols="7">
          {{ $tc('contentNode.storyboard.entity.section.fields.column2') }}
        </v-col>
        <v-col cols="2">
          {{ $tc('contentNode.storyboard.entity.section.fields.column3') }}
        </v-col>
        <v-col cols="1" />
      </v-row>

      <api-sortable v-slot="sortable" :disabled="layoutMode || disabled"
                    :items="sections"
                    @sort="updateSections">
        <api-form :entity="contentNode">
          <v-row dense>
            <v-col cols="2">
              <api-textarea
                :fieldname="`data.sections[${sortable.itemKey}].column1`"
                auto-grow
                rows="2"
                :disabled="layoutMode || disabled"
                :filled="layoutMode" />
            </v-col>
            <v-col cols="7">
              <api-textarea
                :fieldname="`data.sections[${sortable.itemKey}].column2`"
                auto-grow
                rows="4"
                :disabled="layoutMode || disabled"
                :filled="layoutMode" />
            </v-col>
            <v-col cols="2">
              <api-textarea
                :fieldname="`data.sections[${sortable.itemKey}].column3`"
                auto-grow
                rows="2"
                :disabled="layoutMode || disabled"
                :filled="layoutMode" />
            </v-col>
            <v-col cols="1">
              <v-container v-if="!layoutMode && !disabled" class="ma-0 pa-0">
                <v-row no-gutters>
                  <v-col cols="6">
                    <!-- TODO: dialog to ask for confirmation before deletion -->
                    <div class="section-buttons">
                      <v-btn icon x-small
                             color="error"
                             @click="sortable.on.delete(sortable.itemKey)">
                        <v-icon>mdi-delete</v-icon>
                      </v-btn>
                    </div>
                    <v-btn icon x-small
                           class="drag-and-drop-handle">
                      <v-icon>mdi-drag-horizontal-variant</v-icon>
                    </v-btn>
                  </v-col>
                  <v-col cols="6">
                    <div class="section-buttons">
                      <v-btn icon x-small
                             @click="sortable.on.moveUp(sortable.itemKey)">
                        <v-icon>mdi-arrow-up-bold</v-icon>
                      </v-btn>

                      <v-btn icon x-small
                             @click="sortable.on.moveDown(sortable.itemKey)">
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
          <v-btn v-if="!layoutMode && !disabled"
                 icon
                 small
                 class="button-add"
                 color="success"
                 :loading="isAdding"
                 @click="addSection">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </card-content-node>
</template>

<script>
import ApiTextarea from '@/components/form/api/ApiTextarea.vue'
import ApiForm from '@/components/form/api/ApiForm.vue'
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import ApiSortable from '@/components/form/api/ApiSortable.vue'

import { v4 as uuidv4 } from 'uuid'

export default {
  name: 'Storyboard',
  components: {
    CardContentNode,
    ApiForm,
    ApiTextarea,
    ApiSortable
  },
  mixins: [contentNodeMixin],
  props: {
    contentNode: { type: Object, required: true }
  },
  data () {
    return {
      isAdding: false
    }
  },
  computed: {
    sections () {
      return this.api.get(this.contentNode).data.sections
    }
  },
  methods: {
    async addSection () {
      this.isAdding = true

      const sectionId = uuidv4()
      try {
        // TODO: consider adding item to ApiSortable eagerly (should be easy, now that uuid is generated locally)
        await this.contentNode.$patch({
          data: {
            sections: {
              [sectionId]: {
                column1: '',
                column2: '',
                column3: '',
                position: Object.keys(this.sections).length + 1
              }
            }
          }
        })
      } catch (error) {
        console.log(error) // TO DO: display error message in error snackbar/toast
      }

      this.isAdding = false
    },

    async updateSections (payload) {
      try {
        await this.contentNode.$patch({
          data: {
            sections: payload
          }
        })
      } catch (error) {
        console.log(error) // TO DO: display error message in error snackbar/toast
      }
    }
  }
}
</script>

<style scoped>
.section-buttons{
  width:40px;
}

.row-inter {
  height: 4px;
  transition: 0s height;
  transition-duration: 0.5s;
}
.row-inter:hover {
  height: 30px;
  background-color: #EEEEEE;
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
