<template>
  <v-skeleton-loader v-if="camp._meta.loading" type="article" />
  <div v-else>
    <PagesOverview v-model="cnf.contents" @input="onChange">
      <PagesConfig
        v-for="(content, idx) in cnf.contents"
        :key="idx"
        :title="$tc('components.print.printConfigurator.config.' + content.type)"
        :landscape="content.options.orientation === 'L'"
        :multiple="
          contentComponents[content.type].design.multiple ||
          content.options?.periods?.length > 1
        "
        @remove="removeContent(idx)"
      >
        <component
          :is="contentComponents[content.type]"
          :value="content.options"
          :camp="camp"
          @input="onChange"
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
              addContent({
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
      <DownloadClientPdfButton :config="cnf" />
    </v-card-text>

    <v-tabs v-if="isDev" v-model="previewTab" class="px-4">
      <v-tab>Nuxt preview</v-tab>
      <v-tab>Client print preview</v-tab>
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
        <print-preview-client
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
import PrintPreviewClient from './print-client/PrintPreviewClient.vue'
import PrintPreviewNuxt from './print-nuxt/PrintPreviewNuxt.vue'
import Draggable from 'vuedraggable'
import CoverConfig from './config/CoverConfig.vue'
import PicassoConfig from './config/PicassoConfig.vue'
import SummaryConfig from './config/SummaryConfig.vue'
import ProgramConfig from './config/ProgramConfig.vue'
import ActivityConfig from './config/ActivityConfig.vue'
import ActivityListConfig from './config/ActivityListConfig.vue'
import TocConfig from './config/TocConfig.vue'
import PagesOverview from './configurator/PagesOverview.vue'
import PagesConfig from './configurator/PagesConfig.vue'
import DownloadNuxtPdfButton from '@/components/print/print-nuxt/DownloadNuxtPdfButton.vue'
import DownloadClientPdfButton from '@/components/print/print-client/DownloadClientPdfButton.vue'
import { getEnv } from '@/environment.js'
import cloneDeep from 'lodash/cloneDeep'
import VueI18n from '../../plugins/i18n/index.js'
import repairConfig from './repairPrintConfig.js'
import StoryConfig from '@/components/print/config/StoryConfig.vue'
import SafetyConsiderationsConfig from '@/components/print/config/SafetyConsiderationsConfig.vue'
import campShortTitle from '@/common/helpers/campShortTitle.js'

export default {
  name: 'PrintConfigurator',
  components: {
    DownloadClientPdfButton,
    DownloadNuxtPdfButton,
    PagesConfig,
    PagesOverview,
    Draggable,
    PrintPreviewClient,
    PrintPreviewNuxt,
    CoverConfig,
    PicassoConfig,
    SummaryConfig,
    ProgramConfig,
    ActivityConfig,
    TocConfig,
    ActivityListConfig,
  },
  props: {
    camp: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      contentComponents: {
        Cover: CoverConfig,
        Picasso: PicassoConfig,
        Story: StoryConfig,
        SafetyConsiderations: SafetyConsiderationsConfig,
        Program: ProgramConfig,
        Activity: ActivityConfig,
        Toc: TocConfig,
        ActivityList: ActivityListConfig,
      },
      previewTab: null,
    }
  },
  computed: {
    lang() {
      return this.$store.state.lang.language
    },
    cnf() {
      return this.repairConfig(
        this.$store.getters.getLastPrintConfig(this.camp._meta.self, {
          language: this.lang,
          documentName: campShortTitle(this.camp),
          camp: this.camp._meta.self,
          contents: this.defaultContents(),
        })
      )
    },
    isDev() {
      return getEnv().FEATURE_DEVELOPER ?? false
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
  mounted() {
    this.camp.periods().items.forEach((period) => {
      period.days().$reload()
      period.contentNodes().$reload()
    })
  },
  methods: {
    resetConfig() {
      this.$store.commit('setLastPrintConfig', {
        campUri: this.camp._meta.self,
        printConfig: undefined,
      })
    },
    defaultContents() {
      const contents = [
        {
          type: 'Cover',
          options: {},
        },
        {
          type: 'Picasso',
          options: {
            periods: this.camp.periods().items.map((period) => period._meta.self),
            orientation: 'L',
          },
        },
      ]

      this.camp.periods().items.forEach((period) => {
        contents.push({
          type: 'Story',
          options: {
            periods: [period._meta.self],
            contentType: 'Storycontext',
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
    addContent(content) {
      this.cnf.contents.push(content)
      this.onChange()
    },
    removeContent(idx) {
      this.cnf.contents.splice(idx, 1)
      this.onChange()
    },
    onChange() {
      this.$nextTick(() => {
        this.$store.commit('setLastPrintConfig', {
          campUri: this.camp._meta.self,
          printConfig: cloneDeep(this.cnf),
        })
      })
    },
    repairConfig(config) {
      const repairers = Object.fromEntries(
        Object.entries(this.contentComponents).map(([componentName, component]) => [
          componentName,
          component.repairConfig,
        ])
      )

      return repairConfig(
        config,
        this.camp,
        VueI18n.availableLocales,
        this.lang,
        repairers,
        this.defaultContents()
      )
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
