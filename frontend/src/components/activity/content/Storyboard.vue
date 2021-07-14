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

      <api-sortable v-slot="sortable" :disabled="layoutMode || disabled" :collection="sections">
        <api-form :entity="sortable.entity">
          <v-row dense>
            <v-col cols="2">
              <api-textarea
                fieldname="column1"
                auto-grow
                rows="2"
                :disabled="layoutMode || disabled"
                :filled="layoutMode" />
            </v-col>
            <v-col cols="7">
              <api-textarea
                fieldname="column2"
                auto-grow
                rows="4"
                :disabled="layoutMode || disabled"
                :filled="layoutMode" />
            </v-col>
            <v-col cols="2">
              <api-textarea
                fieldname="column3"
                auto-grow
                rows="2"
                :disabled="layoutMode || disabled"
                :filled="layoutMode" />
            </v-col>
            <v-col cols="1">
              <v-container v-if="!layoutMode && !disabled" class="ma-0 pa-0">
                <v-row no-gutters>
                  <v-col cols="6">
                    <div class="section-buttons">
                      <dialog-entity-delete :entity="sortable.entity">
                        <template #activator="{ on }">
                          <v-btn icon
                                 x-small
                                 color="error"
                                 v-on="on">
                            <v-icon>mdi-delete</v-icon>
                          </v-btn>
                        </template>
                      </dialog-entity-delete>
                    </div>
                    <v-btn icon x-small
                           class="drag-and-drop-handle">
                      <v-icon>mdi-drag-horizontal-variant</v-icon>
                    </v-btn>
                  </v-col>
                  <v-col cols="6">
                    <div class="section-buttons">
                      <v-btn icon x-small
                             @click="sortable.on.moveUp(sortable.entity)">
                        <v-icon>mdi-arrow-up-bold</v-icon>
                      </v-btn>

                      <v-btn icon x-small
                             @click="sortable.on.moveDown(sortable.entity)">
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
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import ApiSortable from '@/components/form/api/ApiSortable.vue'

export default {
  name: 'Storyboard',
  components: {
    CardContentNode,
    ApiForm,
    ApiTextarea,
    DialogEntityDelete,
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
      return this.api.get(this.contentNode).sections
    }
  },
  methods: {
    async addSection () {
      this.isAdding = true
      await this.api.post(this.contentNode.sections(), {
        contentNodeId: this.contentNode.id,
        pos: this.contentNode.sections().items.length // add at the end of the array
      })
      await this.refreshContent() // refresh node content (reloading section array)
      this.isAdding = false
    },
    async refreshContent () {
      await this.api.reload(this.contentNode)
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
