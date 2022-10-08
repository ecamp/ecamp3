<template>
  <div>
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else>
      <v-container>
        <v-row>
          <v-col cols="12" md="8">
            <div>
              <DownloadNuxtPdfButton :config="cnf" class="mr-3 float-left" />
              <DownloadReactPdfButton :config="cnf" class="mr-3" />
            </div>
            <v-list>
              <draggable v-model="cnf.contents" handle=".handle">
                <v-list-item v-for="(content, idx) in cnf.contents" :key="idx">
                  <v-list-item-icon>
                    <v-icon class="handle">mdi-drag-horizontal-variant</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title>
                      <h3>
                        {{
                          $tc('components.print.printConfigurator.config.' + content.type)
                        }}
                      </h3>
                    </v-list-item-title>
                    <component
                      :is="contentComponents[content.type]"
                      v-model="content.options"
                      :camp="camp()"
                    />
                  </v-list-item-content>
                  <v-list-item-action>
                    <v-btn icon @click="cnf.contents.splice(idx, 1)">
                      <v-icon color="red">mdi-delete</v-icon>
                    </v-btn>
                  </v-list-item-action>
                </v-list-item>
              </draggable>
            </v-list>
            <br />
            <v-menu>
              <template #activator="{ on, attrs }">
                <button-add v-bind="attrs" v-on="on" />
              </template>
              <v-list>
                <v-list-item
                  v-for="(component, idx) in contentComponents"
                  :key="idx"
                  @click="
                    cnf.contents.push({
                      type: idx,
                      options: component.defaultOptions(),
                    })
                  "
                >
                  <v-list-item-title>
                    {{ $tc('components.print.printConfigurator.config.' + idx) }}
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </v-col>
          <v-col cols="12" md="4">
            <v-expansion-panels v-if="isDev">
              <v-expansion-panel>
                <v-expansion-panel-header>View Print-Config</v-expansion-panel-header>
                <v-expansion-panel-content>
                  <pre>{{ cnf }}</pre>
                </v-expansion-panel-content>
              </v-expansion-panel>
            </v-expansion-panels>
          </v-col>
        </v-row>
        <v-row v-if="isDev">
          <v-col cols="12">
            <v-tabs v-model="previewTab">
              <v-tab>Nuxt preview</v-tab>
              <v-tab>React preview</v-tab>
              <v-tab-item>
                <print-preview-nuxt
                  v-if="previewTab === 0"
                  :config="cnf"
                  width="100%"
                  height="600"
                  class="my-4"
                />
              </v-tab-item>
              <v-tab-item>
                <print-preview-react
                  v-if="previewTab === 1"
                  :config="cnf"
                  width="100%"
                  height="600"
                  class="my-4"
                />
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
import CoverConfig from './config/CoverConfig.vue'
import PicassoConfig from './config/PicassoConfig.vue'
import StoryConfig from './config/StoryConfig.vue'
import ProgramConfig from './config/ProgramConfig.vue'
import ActivityConfig from './config/ActivityConfig.vue'
import TocConfig from './config/TocConfig.vue'

export default {
  name: 'PrintConfigurator',
  components: {
    Draggable,
    PrintPreviewReact,
    PrintPreviewNuxt,
    CoverConfig,
    PicassoConfig,
    StoryConfig,
    ProgramConfig,
    ActivityConfig,
    TocConfig,
  },
  props: {
    camp: {
      type: Function,
      required: true,
    },
  },
  data() {
    return {
      contentComponents: {
        Cover: CoverConfig,
        Picasso: PicassoConfig,
        Story: StoryConfig,
        Program: ProgramConfig,
        Activity: ActivityConfig,
        Toc: TocConfig,
      },
      cnf: {
        language: '',
        documentName: this.camp().title + '.pdf',
        camp: this.camp()._meta.self,
        contents: this.defaultContents(),
      },
      previewTab: null,
    }
  },
  computed: {
    lang() {
      return this.$store.state.lang.language
    },
    isDev() {
      return process.env.NODE_ENV === 'development'
    },
  },
  watch: {
    lang: {
      handler(language) {
        this.cnf.language = language
      },
      immediate: true,
    },
  },
  methods: {
    defaultContents() {
      const contents = [
        {
          type: 'Cover',
          options: {},
        },
        {
          type: 'Picasso',
          options: {
            periods: this.camp()
              .periods()
              .items.map((period) => period._meta.self),
            orientation: 'L',
          },
        },
      ]

      this.camp()
        .periods()
        .items.forEach((period) => {
          contents.push({
            type: 'Story',
            options: {
              periods: [period._meta.self],
            },
          })
          contents.push({
            type: 'Program',
            options: {
              periods: [period._meta.self],
            },
          })
        })

      contents.push({
        type: 'Toc',
        options: {},
      })

      return contents
    },
  },
}
</script>

<style scoped></style>
