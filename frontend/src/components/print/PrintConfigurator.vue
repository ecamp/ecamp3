<template>
  <div>
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else>
      <h3>{{ $tc('components.camp.campPrint.selectPrintPreview') }}</h3>

      <v-container>
        <v-row>
          <v-col cols="12" md="8">
            <v-list>
              <draggable v-model="cnf.contents" handle=".handle">
                <v-list-item v-for="(content, idx) in cnf.contents" :key="idx">
                  <v-list-item-icon>
                    <v-icon class="handle">mdi-drag-horizontal-variant</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title>
                      <h3>{{ $tc('components.print.printConfigurator.config.' + content.type) }}</h3>
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
                <button-add
                  v-bind="attrs"
                  v-on="on" />
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
                    {{ $tc('components.print.printConfigurator.config.' + component.name) }}
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </v-col>
          <v-col cols="12" md="4">
            <v-expansion-panels>
              <v-expansion-panel>
                <v-expansion-panel-header>View Print-Config</v-expansion-panel-header>
                <v-expansion-panel-content>
                  <pre>{{ cnf }}</pre>
                </v-expansion-panel-content>
              </v-expansion-panel>
            </v-expansion-panels>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <v-tabs v-model="previewTab">
              <v-tab>Print with Nuxt</v-tab>
              <v-tab>Print with React</v-tab>
              <v-tab-item>
                <print-preview-nuxt v-if="previewTab === 0"
                                    :config="cnf"
                                    width="100%"
                                    height="600"
                                    class="my-4" />
              </v-tab-item>
              <v-tab-item>
                <print-preview-react v-if="previewTab === 1"
                                     :config="cnf"
                                     width="100%"
                                     height="600"
                                     class="my-4" />
              </v-tab-item>
            </v-tabs>
          </v-col>
        </v-row>
      </v-container>
    </div>
  </div>
</template>

<script>
import PrintPreviewReact from './print-react/PrintPreviewReact.vue'
import PrintPreviewNuxt from './print-nuxt/PrintPreviewNuxt.vue'
import Draggable from 'vuedraggable'
import Cover from './config/CoverConfig.vue'
import Picasso from './config/PicassoConfig.vue'
import Story from './config/StoryConfig.vue'
import Program from './config/ProgramConfig.vue'
import Activity from './config/ActivityConfig.vue'
import Toc from './config/TocConfig.vue'

export default {
  name: 'PrintConfigurator',
  components: {
    Draggable,
    PrintPreviewReact,
    PrintPreviewNuxt,
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
        camp: this.camp()._meta.self,
        contents: this.defaultContents()
      },
      previewTab: null
    }
  },
  computed: {
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
