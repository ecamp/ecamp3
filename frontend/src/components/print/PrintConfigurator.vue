<template>
  <div v-resizeobserver:0.immediate="onResize" class="e-print-configurator">
    <v-skeleton-loader v-if="loading" type="article" class="pa-4" />
    <template v-else>
      <v-tabs v-if="!isWide" v-model="activeTab" class="e-print-configurator__tabs">
        <v-tab value="config">{{
          $t('components.print.printConfigurator.configuration')
        }}</v-tab>
        <v-tab value="preview">{{
          $t('components.print.printConfigurator.preview')
        }}</v-tab>
      </v-tabs>
      <div class="e-print-configurator__columns">
        <div
          v-show="isWide || activeTab === 'config'"
          class="e-print-configurator__config"
        >
          <div class="e-print-configurator__settings pa-4">
            <h2 class="text-subtitle-1 mb-2">
              {{ $t('components.print.printConfigurator.options') }}
            </h2>
            <e-checkbox
              v-model="cnf.options.pageNumbers"
              :label="$t('components.print.printConfigurator.pageNumbers')"
              @update:model-value="onChange"
            />
            <e-checkbox
              v-model="cnf.options.compact"
              :label="$t('components.print.printConfigurator.compact')"
              @update:model-value="onChange"
            />
            <e-select
              v-model="cnf.options.pageSize"
              class="mt-4"
              :items="pageSizes"
              :label="$t('components.print.printConfigurator.fontSize')"
              @update:model-value="onChange"
            />
          </div>
          <PagesOverview v-model="cnf.contents" @update:model-value="onChange">
            <template #item="{ element: content, index: idx }">
              <PagesConfig
                :title="$t('components.print.printConfigurator.config.' + content.type)"
                :landscape="content.options.orientation === 'L'"
                :multiple="
                  contentComponents[content.type].design.multiple ||
                  content.options?.periods?.length > 1
                "
                @remove="removeContent(idx)"
              >
                <component
                  :is="contentComponents[content.type]"
                  :model-value="content.options"
                  :camp="camp"
                  @update:model-value="onChangeContentConfig(idx, $event)"
                />
              </PagesConfig>
            </template>

            <v-menu offset-y rounded="lg" offset-overflow>
              <template #activator="{ props }">
                <PagesConfig
                  id="page-config"
                  :title="$t('components.print.printConfigurator.add')"
                  multiple
                  template
                  v-bind="props"
                />
              </template>
              <v-list>
                <v-list-item
                  v-for="(component, idx) in contentComponents"
                  :key="idx"
                  @click="
                    addContent({
                      type: idx,
                      options: component.defaultOptions(camp),
                    })
                  "
                >
                  <v-list-item-title>
                    {{ $t('components.print.printConfigurator.config.' + idx) }}
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </PagesOverview>

          <v-expansion-panels v-if="isDev" flat class="e-print-configurator__cnf">
            <v-expansion-panel class="bg-transparent rounded-0">
              <v-expansion-panel-title class="subtitle py-2"
                >View Print-Config</v-expansion-panel-title
              >
              <v-expansion-panel-text>
                <pre style="font-size: 12px">{{ prettyConfig }}</pre>
              </v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>
        </div>
        <div
          v-show="isWide || activeTab === 'preview'"
          class="e-print-configurator__preview"
        >
          <button
            v-if="!previewEnabled"
            type="button"
            class="e-print-configurator__preview-activator"
            @click="previewEnabled = true"
          >
            <span class="e-print-configurator__preview-paper">
              <v-icon icon="mdi-file-pdf-box" size="x-large" />
              <span>{{ $t('components.print.printConfigurator.showPreview') }}</span>
            </span>
          </button>
          <print-preview v-else :config="cnf" />
        </div>
      </div>
      <div class="e-print-configurator__footer pa-4">
        <v-card-text class="e-print-configurator__notice pa-0">
          <v-alert
            variant="tonal"
            border="start"
            size="small"
            color="info"
            density="compact"
          >
            <details class="text-blue-darken-3">
              <summary class="font-weight-bold">
                <v-icon icon="mdi-information-outline" size="small" class="float-end" />
                {{ $t('components.print.printConfigurator.layoutInfoTitle') }}
              </summary>
              <i18n-t
                keypath="components.print.printConfigurator.layoutInfo"
                scope="global"
              >
                <template #contactLink>
                  <a
                    :href="contactLink"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-inherit"
                    >{{ $t('components.print.printConfigurator.contactLink') }}</a
                  >
                </template>
              </i18n-t>
            </details>
          </v-alert>
        </v-card-text>
        <DownloadPdfButton :config="cnf" class="e-print-configurator__download" />
      </div>
    </template>
  </div>
</template>

<script>
import PrintPreview from './generator/PrintPreview.vue'
import CoverConfig from './config/CoverConfig.vue'
import PicassoConfig from './config/PicassoConfig.vue'
import SummaryConfig from './config/SummaryConfig.vue'
import ProgramConfig from './config/ProgramConfig.vue'
import ActivityConfig from './config/ActivityConfig.vue'
import ActivityListConfig from './config/ActivityListConfig.vue'
import TocConfig from './config/TocConfig.vue'
import PagesOverview from './configurator/PagesOverview.vue'
import PagesConfig from './configurator/PagesConfig.vue'
import DownloadPdfButton from '@/components/print/generator/DownloadPdfButton.vue'
import { getEnv } from '@/environment.js'
import { cloneDeep } from 'lodash-es'
import { componentI18n } from '../../plugins/i18n/index.js'
import repairConfig from './repairPrintConfig.js'
import StoryConfig from '@/components/print/config/StoryConfig.vue'
import SafetyConsiderationsConfig from '@/components/print/config/SafetyConsiderationsConfig.vue'
import campShortTitle from '@/helpers/campShortTitle.js'
import jsonStringifyReactiveValue from '@/components/print/jsonStringifyReactiveValue.js'

export default {
  name: 'PrintConfigurator',
  components: {
    DownloadPdfButton,
    PagesConfig,
    PagesOverview,
    PrintPreview,
    CoverConfig,
    PicassoConfig,
    SummaryConfig,
    ProgramConfig,
    ActivityConfig,
    TocConfig,
    ActivityListConfig,
  },
  provide() {
    return {
      loadingEndpoints: this.loadingEndpoints,
    }
  },
  props: {
    camp: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      loading: true,
      cnf: null,
      loadingEndpoints: {
        activities: true,
        categories: true,
        periods: true,
        days: true,
        campCollaborations: true,
        progressLabels: true,
        scheduleEntries: true,
      },
      prettyConfig: '',
      containerWidth: 0,
      activeTab: 'config',
      previewEnabled: false,
    }
  },
  computed: {
    isWide() {
      return this.containerWidth >= 900
    },
    contactLink() {
      return getEnv().CONTACT_LINK
    },
    contentComponents() {
      return {
        Cover: CoverConfig,
        Picasso: PicassoConfig,
        Story: StoryConfig,
        SafetyConsiderations: SafetyConsiderationsConfig,
        Program: ProgramConfig,
        Activity: ActivityConfig,
        Toc: TocConfig,
        ActivityList: ActivityListConfig,
      }
    },
    lang() {
      return this.$store.state.lang.language
    },
    lastPrintConfig() {
      return this.$store.getters.getLastPrintConfig(this.camp._meta.self, null)
    },
    currentConfig() {
      return this.lastPrintConfig ?? this.defaultConfig
    },
    defaultConfig() {
      return {
        language: this.lang,
        documentName: campShortTitle(this.camp),
        options: { pageNumbers: false, pageSize: 'A4', compact: false },
        camp: this.camp._meta.self,
        contents: this.defaultContents(),
      }
    },
    pageSizes() {
      return ['A5', 'A4', 'A3'].map((size) => ({
        value: size,
        text: this.$t(`components.print.printConfigurator.fontSizes.${size}`),
      }))
    },
    isDev() {
      return getEnv().FEATURE_DEVELOPER ?? false
    },
  },
  watch: {
    isWide() {
      this.activeTab = 'config'
    },
    activeTab(value) {
      if (value === 'preview') this.previewEnabled = true
    },
    lang: {
      handler(language) {
        if (!this.cnf) return
        this.cnf.language = language
        this.onChange()
      },
      immediate: true,
    },
  },
  async mounted() {
    await this.loadEndpointData('periods')
    this.setRepairedConfig(this.currentConfig)
    this.loading = false
    await this.loadRemainingPrintData()
  },
  methods: {
    onResize({ width }) {
      this.containerWidth = width
    },
    async loadRemainingPrintData() {
      const periods = this.camp.periods().items
      await Promise.all([
        this.loadEndpointData(
          'scheduleEntries',
          Promise.all(periods.map((period) => period.scheduleEntries()._meta.load))
        ),
        this.loadEndpointData('activities'),
      ])

      await Promise.all([
        this.loadEndpointData(
          'days',
          Promise.all(periods.map((period) => period.days()._meta.load))
        ),
        Promise.all(periods.map((period) => period.contentNodes()._meta.load)),
        this.loadEndpointData('categories'),
        this.loadEndpointData('campCollaborations'),
        this.loadEndpointData('progressLabels'),
      ])

      this.setRepairedConfig(this.cnf)
    },
    setRepairedConfig(config) {
      const repaired = this.repairConfig(config)
      this.cnf = repaired
      if (this.isDev) {
        this.prettyConfig = jsonStringifyReactiveValue(this.cnf, 2)
      }
      if (jsonStringifyReactiveValue(config) !== jsonStringifyReactiveValue(repaired)) {
        this.onChange()
      }
    },
    async loadEndpointData(endpoint, load = this.camp[endpoint]()._meta.load) {
      await load
      this.loadingEndpoints[endpoint] = false
    },
    createConfig() {
      return this.repairConfig(this.defaultConfig)
    },
    resetConfig() {
      this.$store.commit('setLastPrintConfig', {
        campUri: this.camp._meta.self,
        printConfig: undefined,
      })
      this.cnf = this.createConfig()
      if (this.isDev) {
        this.prettyConfig = jsonStringifyReactiveValue(this.cnf, 2)
      }
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
          printConfig: cloneDeep(JSON.parse(jsonStringifyReactiveValue(this.cnf))),
        })
      })
      if (this.isDev) {
        this.prettyConfig = jsonStringifyReactiveValue(this.cnf, 2)
      }
    },
    onChangeContentConfig(index, value) {
      this.cnf.contents[index].options = value
      this.onChange()
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
        componentI18n.availableLocales,
        this.lang,
        repairers,
        this.defaultContents()
      )
    },
  },
}
</script>

<style scoped lang="scss">
.e-print-configurator {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 0;
}

.e-print-configurator__tabs,
.e-print-configurator__footer {
  flex: none;
}

.e-print-configurator__columns {
  display: flex;
  flex: 1 1 0;
  min-height: 0;
}

.e-print-configurator__config,
.e-print-configurator__preview {
  flex: 1 1 0;
  min-width: 0;
  min-height: 0;
  overflow: auto;
}

.e-print-configurator__preview {
  display: flex;
  flex-direction: column;
  border-left: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
  background: #263238;
}

.e-print-configurator__preview-activator {
  display: flex;
  flex: 1 1 0;
  align-items: center;
  justify-content: center;
  width: 100%;
  min-height: 0;
  padding: 24px;
  color: #455a64;
  cursor: pointer;
}

.e-print-configurator__preview-activator:focus-visible {
  outline: 2px solid white;
  outline-offset: -4px;
}

.e-print-configurator__preview-paper {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: min(100%, 420px);
  max-width: 100%;
  aspect-ratio: 210 / 297;
  border: 1px solid #e5ddc8;
  border-radius: 12px;
  background: linear-gradient(145deg, #fffef8, #f4eedc);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  gap: 8px;
}

.e-print-configurator__preview-activator:hover .e-print-configurator__preview-paper {
  background: linear-gradient(145deg, #fff, #fbf7e9);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
}

.e-print-configurator__footer {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  border-top: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
  background: rgb(var(--v-theme-surface));
}

.e-print-configurator__notice {
  flex: 1 1 300px;
  min-width: 0;
  max-width: 700px;
  max-height: 30vh;
  overflow: auto;
}

.e-print-configurator__notice summary {
  cursor: pointer;
}

.e-print-configurator__download {
  margin-left: auto;
}

.e-print-configurator__cnf {
  &:deep(.v-expansion-panel-title) {
    font-family: monospace;
    border-top: 1px solid rgba(0, 0, 0, 0.2);
    border-bottom: 1px solid rgba(0, 0, 0, 0.2);
  }

  &:deep(.v-expansion-panel-title--active) {
    border-bottom: none;
  }

  &:deep(.v-expansion-panel-text) {
    border-bottom: 1px solid rgba(0, 0, 0, 0.2);
  }
}
</style>
