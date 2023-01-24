<template>
  <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
  <div v-else>
    <PagesOverview v-model="cnf.contents">
      <PagesConfig
        v-for="(content, idx) in cnf.contents"
        :key="idx"
        :title="$tc('components.print.printConfigurator.config.' + content.type)"
        :landscape="content.options.orientation === 'L'"
        :multiple="
          contentComponents[content.type].design.multiple ||
          content.options?.periods?.length > 1
        "
        @remove="cnf.contents.splice(idx, 1)"
      >
        <component
          :is="contentComponents[content.type]"
          v-model="content.options"
          :camp="camp()"
        />
      </PagesConfig>

      <v-menu offset-y rounded="lg" offset-overflow>
        <template #activator="{ on, attrs }">
          <PagesConfig
            id="page-config"
            :title="$tc('components.print.printConfigurator.add')"
            multiple
            template
            v-bind="attrs"
            v-on="on"
          />
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

      <template #drawer>
        <v-expansion-panels v-if="isDev" flat class="e-print-configurator__cnf">
          <v-expansion-panel class="transparent rounded-0">
            <v-expansion-panel-header class="subtitle py-2"
              >View Print-Config
            </v-expansion-panel-header>
            <v-expansion-panel-content>
              <pre style="font-size: 12px">{{ cnf }}</pre>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
      </template>
    </PagesOverview>

    <v-card-text class="e-button-container">
      <DownloadNuxtPdfButton :config="cnf" />
      <DownloadReactPdfButton :config="cnf" />
    </v-card-text>

    <v-tabs v-if="isDev" v-model="previewTab" class="px-4">
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
import PagesOverview from './configurator/PagesOverview.vue'
import PagesConfig from './configurator/PagesConfig.vue'
import DownloadNuxtPdfButton from '@/components/print/print-nuxt/DownloadNuxtPdfButton.vue'
import DownloadReactPdfButton from '@/components/print/print-react/DownloadReactPdfButton.vue'

export default {
  name: 'PrintConfigurator',
  components: {
    DownloadReactPdfButton,
    DownloadNuxtPdfButton,
    PagesConfig,
    PagesOverview,
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
      return window.environment.FEATURE_DEVELOPER ?? false
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

<style scoped lang="scss">
.e-print-configurator__cnf {
  &:deep {
    .v-expansion-panel-header {
      font-family: monospace;
      border-top: 1px solid rgba(0, 0, 0, 0.2);
      border-bottom: 1px solid rgba(0, 0, 0, 0.2);
    }

    .v-expansion-panel-header--active {
      border-bottom: none;
    }

    .v-expansion-panel-content {
      border-bottom: 1px solid rgba(0, 0, 0, 0.2);
    }
  }
}
</style>
