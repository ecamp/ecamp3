<template>
  <div>
    <v-container fluid>
      <v-row no-gutters class="text-subtitle-2">
        <v-col cols="2">
          {{ $tc('activityContent.storyboard.entity.section.fields.column1') }}
        </v-col>
        <v-col cols="7">
          {{ $tc('activityContent.storyboard.entity.section.fields.column2') }}
        </v-col>
        <v-col cols="2">
          {{ $tc('activityContent.storyboard.entity.section.fields.column3') }}
        </v-col>
        <v-col cols="1" />
      </v-row>
      <draggable
        v-model="sorting.hrefList"
        ghost-class="ghost"
        handle=".drag-and-drop-handle"
        :animation="200"
        :force-fallback="true"
        @sort="onSort"
        @start="dragging = true"
        @end="dragging = false">
        <!-- disable transition for drag&drop as draggable already comes with its own anmations -->
        <transition-group :name="!dragging ? 'flip-list' : null" tag="div">
          <div v-for="section in sortedSections" :key="section._meta.self">
            <!-- add before -->
            <v-row no-gutters class="row-inter" justify="center">
              <v-col cols="1">
                <v-btn icon
                       small
                       class="button-add"
                       color="success"
                       @click="addSection">
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </v-col>
            </v-row>

            <api-form :entity="section">
              <v-row dense>
                <v-col cols="2">
                  <api-textarea
                    fieldname="column1"
                    auto-grow
                    rows="2" />
                </v-col>
                <v-col cols="7">
                  <api-textarea
                    fieldname="column2"
                    auto-grow
                    rows="4" />
                </v-col>
                <v-col cols="2">
                  <api-textarea
                    fieldname="column3"
                    auto-grow
                    rows="2" />
                </v-col>
                <v-col cols="1">
                  <v-container>
                    <v-row no-gutters>
                      <v-col>
                        <div class="float-right section-buttons">
                          <dialog-entity-delete :entity="section">
                            <template v-slot:activator="{ on }">
                              <v-btn icon
                                     small
                                     color="error"
                                     class="float-right"
                                     v-on="on">
                                <v-icon>mdi-delete</v-icon>
                              </v-btn>
                            </template>
                          </dialog-entity-delete>
                        </div>
                      </v-col>
                      <v-col>
                        <div class="float-right section-buttons">
                          <v-btn icon small
                                 class="float-right"
                                 @click="sectionUp(section)">
                            <v-icon>mdi-arrow-up-bold</v-icon>
                          </v-btn>
                          <v-btn icon small
                                 class="float-right drag-and-drop-handle">
                            <v-icon>mdi-drag-horizontal-variant</v-icon>
                          </v-btn>
                          <v-btn icon small
                                 class="float-right"
                                 @click="sectionDown(section)">
                            <v-icon>mdi-arrow-down-bold</v-icon>
                          </v-btn>
                        </div>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-col>
              </v-row>
            </api-form>
          </div>
        </transition-group>
      </draggable>
      <!-- add at end position -->
      <v-row no-gutters justify="center">
        <v-col cols="1">
          <v-btn icon
                 small
                 class="button-add"
                 color="success"
                 @click="addSection">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import ApiTextarea from '@/components/form/api/ApiTextarea'
import ApiForm from '@/components/form/api/ApiForm'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete'
import draggable from 'vuedraggable'
import { isEqual } from 'lodash'

export default {
  name: 'Storyboard',
  components: {
    ApiForm,
    ApiTextarea,
    DialogEntityDelete,
    draggable
  },
  props: {
    activityContent: { type: Object, required: true }
  },
  data () {
    return {
      dragging: false,
      sorting: {
        dirty: false,
        hrefList: []
      }
    }
  },
  computed: {
    // retrieve all relevant entitieys from external (incl. filtering and sorting)
    sections () {
      return this.activityContent.sections().items.sort((a, b) => a.pos - b.pos)
    },

    // locally sorted entities (sorted as per loal hrefList)
    sortedSections () {
      return this.sorting.hrefList.map(href => this.api.get(href))
    }
  },
  watch: {
    sections: {
      handler: function (sections) {
        const hrefList = sections.map(entry => entry._meta.self)

        // update local sorting with external sorting if not dirty
        if (!this.sorting.dirty) {
          this.sorting.hrefList = hrefList

        // remove dirty flag if external sorting is equal to local sorting (e.g. saving to API was successful)
        } else if (isEqual(this.sorting.hrefList, hrefList)) {
          this.sorting.dirty = false
        }
      },
      immediate: true
    }
  },
  methods: {
    async addSection () {
      // this.isAdding = true
      await this.api.post('/content-type/storyboards', {
        activityContentId: this.activityContent.id,
        pos: 100
      })
      await this.refreshContent()
      // this.isAdding = false
    },
    async refreshContent () {
      await this.api.reload(this.activityContent)
    },
    async sectionUp (section) {
      const list = this.sorting.hrefList
      const index = list.indexOf(section._meta.self)

      // cannot move first entry up
      if (index > 0) {
        const previousItem = list[index - 1]
        this.$set(list, index - 1, list[index])
        this.$set(list, index, previousItem)
      }

      this.saveLocalSorting()
    },
    async sectionDown (section) {
      const list = this.sorting.hrefList
      const index = list.indexOf(section._meta.self)

      // cannot move last entry down
      if (index >= 0 && index < (list.length - 1)) {
        const nextItem = list[index + 1]
        this.$set(list, index + 1, list[index])
        this.$set(list, index, nextItem)
      }

      this.saveLocalSorting()
    },

    /**
     * Triggeres on every sorting change
     */
    onSort (event) {
      this.saveLocalSorting()
    },

    /**
     * Saves local list sorting to API
     */
    saveLocalSorting () {
      this.sorting.dirty = true

      /**
      Compiles proper patchList object in the form of
      {
        'id1': { pos: 0 },
        'id2': { pos: 1 },
        ...
      }
      */
      const patchData = this.sortedSections.map((section, index) => [section.id, { pos: index }])
      const patchDataObj = Object.fromEntries(patchData)

      this.api.patch('/content-type/storyboards', patchDataObj)
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

.flip-list-move {
  transition: transform 0.5s;
  opacity: 0.5;
  background: #c8ebfb;
}

.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}

.drag-and-drop-handle {
  cursor: grab;
}

</style>
