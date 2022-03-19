<template>
  <div>
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else>
      <h3>{{ $tc('components.camp.campPrint.selectPrintPreview') }}</h3>

      <v-list>
        <draggable v-model="cnf.contents" handle=".handle">
          <v-list-item v-for="(content, idx) in cnf.contents" :key="idx">
            <v-list-item-icon>
              <v-icon class="handle">mdi-drag-horizontal-variant</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>
                <h3>{{ content.type }}</h3>
              </v-list-item-title>
              <component :is="content.type"
                         v-model="content.options"
                         :camp="camp()" />
            </v-list-item-content>
            <v-list-item-action>
              <v-btn icon @click="cnf.contents.splice(idx, 1)">
                <v-icon color="red">mdi-delete</v-icon>
              </v-btn>
            </v-list-item-action>
          </v-list-item>
        </draggable>
      </v-list>
      <br>
      <v-menu>
        <template #activator="{ on, attrs }">
          <v-btn
            color="primary"
            dark
            v-bind="attrs"
            v-on="on">
            Add
          </v-btn>
        </template>
        <v-list>
          <v-list-item
            v-for="(component, idx) in contentComponents"
            :key="idx"
            @click="cnf.contents.push({
              type: component.name,
              options: component.defaultOptions()
            })">
            <v-list-item-title>
              {{ component.name }}
            </v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <br>
      <br>
      <hr>
      <pre>
{{ cnf }}

      </pre>
      <div>
        <v-btn color="primary" class="mt-5"
               :href="previewUrl"
               target="_blank">
          {{ $tc('components.camp.campPrint.openPrintPreview') }}
        </v-btn>

        <local-print-preview :config="{ camp: camp.bind(this), ...cnf }"
                             width="100%"
                             height="500"
                             class="mt-4" />
      </div>
    </div>
  </div>
</template>

<script>
import LocalPrintPreview from '../print/LocalPrintPreview.vue'
import Draggable from 'vuedraggable'
import Cover from './print/Cover.vue'
import Picasso from './print/Picasso.vue'
import Story from './print/Story.vue'
import Program from './print/Program.vue'
import Activity from './print/Activity.vue'
import Toc from './print/Toc.vue'

const PRINT_SERVER = window.environment.PRINT_SERVER

export default {
  name: 'CampPrint',
  components: {
    Draggable,
    LocalPrintPreview,
    Cover,
    Picasso,
    Story,
    Program,
    Activity,
    Toc
  },
  props: {
    camp: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      // OLD
      config: {
        showFrontpage: true,
        showToc: true,
        showPicasso: true,
        showDailySummary: true,
        showStoryline: true,
        showActivities: true,
        camp: this.camp.bind(this)
      },

      // NEW
      contentComponents: [
        Cover,
        Picasso,
        Story,
        Program,
        Activity,
        Toc
      ],
      cnf: {
        language: '',
        documentName: this.camp().title + '.pdf',
        contents: [
        ]
      }
    }
  },
  computed: {
    previewUrl () {
      const configGetParams = Object.entries(this.config).map(([key, val]) => `${key}=${val}`).join('&')
      return `${PRINT_SERVER}/?camp=${this.camp().id}&pagedjs=true&${configGetParams}&lang=${this.lang}`
    },
    lang () {
      return this.$store.state.lang.language
    },
    dataLoading () {
      return this.camp()._meta.loading ||
        this.camp().periods()._meta.loading ||
        this.camp().periods().items.some(period => {
          return period._meta.loading ||
            period.scheduleEntries()._meta.loading ||
            period.scheduleEntries().items.some(scheduleEntry => {
              return scheduleEntry._meta.loading ||
                scheduleEntry.activity()._meta.loading ||
                scheduleEntry.activity().category()._meta.loading ||
                scheduleEntry.activity().activityResponsibles()._meta.loading ||
                scheduleEntry.activity().activityResponsibles().items.some(responsible => {
                  return responsible._meta.loading ||
                    responsible.campCollaboration()._meta.loading ||
                    (responsible.campCollaboration().user() !== null && responsible.campCollaboration().user()._meta.loading)
                }) ||
                scheduleEntry.activity().contentNodes()._meta.loading ||
                scheduleEntry.activity().contentNodes().items.some(contentNode => {
                  return contentNode._meta.loading ||
                    contentNode.contentType()._meta.loading
                })
            })
        }) ||
        this.camp().materialLists()._meta.loading ||
        this.camp().materialLists().items.some(materialList => {
          return materialList._meta.loading
        })
    }
  },
  watch: {
    lang: {
      handler (language) {
        this.cnf.language = language
      },
      immediate: true
    }
  }
}
</script>

<style scoped>
</style>
