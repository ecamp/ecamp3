<template>
  <div>
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else>
      <h3>{{ $tc('components.camp.campPrint.selectPrintPreview') }}</h3>

      <v-container>
        <v-row>
          <v-col cols="12" lg="4">
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
          </v-col>
          <v-col cols="12" lg="8">
            <div>
              <v-btn color="primary"
                     :href="previewUrl"
                     target="_blank">
                {{ $tc('components.camp.campPrint.openPrintPreview') }}
              </v-btn>

              <local-print-preview :config="{ camp: camp.bind(this), ...cnf }"
                                   width="100%"
                                   height="600"
                                   class="my-4" />

              <v-expansion-panels>
                <v-expansion-panel>
                  <v-expansion-panel-header>View Print-Config</v-expansion-panel-header>
                  <v-expansion-panel-content>
                    <pre>{{ cnf }}</pre>
                  </v-expansion-panel-content>
                </v-expansion-panel>
              </v-expansion-panels>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </div>
  </div>
</template>

<script>
import LocalPrintPreview from './print-react/LocalPrintPreview.vue'
import Draggable from 'vuedraggable'
import Cover from './config/Cover.vue'
import Picasso from './config/Picasso.vue'
import Story from './config/Story.vue'
import Program from './config/Program.vue'
import Activity from './config/Activity.vue'
import Toc from './config/Toc.vue'

const PRINT_SERVER = window.environment.PRINT_SERVER

export default {
  name: 'PrintConfigurator',
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
        contents: this.defaultContents()
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
  },
  methods: {
    defaultContents () {
      const contents = [
        {
          type: 'Cover',
          options: {}
        },
        {
          type: 'Picasso',
          options: {
            periods: this.camp().periods().items.map(period => period._meta.self),
            orientation: 'L'
          }
        }
      ]

      this.camp().periods().items.forEach(period => {
        contents.push({
          type: 'Story',
          options: {
            periods: [period._meta.self]
          }
        })
        contents.push({
          type: 'Program',
          options: {
            periods: [period._meta.self]
          }
        })
      })

      contents.push({
        type: 'Toc',
        options: {}
      })

      return contents
    }
  }
}
</script>

<style scoped>
</style>
