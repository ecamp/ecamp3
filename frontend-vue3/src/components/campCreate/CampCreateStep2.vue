<template>
  <v-stepper-content :step="2" class="pa-0">
    <ValidationObserver v-slot="{ handleSubmit, valid, validate }">
      <e-form name="camp">
        <v-form ref="form" @submit.prevent="handleSubmit(() => $emit('create-camp'))">
          <v-card-text>
            <server-error :server-error="serverError" />

            <e-select
              v-model="selectedPrototypeValue"
              :vee-rules="{ required: true }"
              :skip-if-empty="false"
              :label="$t('entity.camp.prototype')"
              :hint="prototypeHint"
              persistent-hint
              :items="campTemplates"
              :menu-props="{ offsetY: true }"
              data-testid="prototype-select"
            />
            <div
              v-if="selectedPrototypeValue === 'other'"
              class="e-form-container d-flex gap-2"
            >
              <e-text-field
                ref="copyCampUrlInput"
                v-model="copyCampUrl"
                :vee-rules="{ required: true }"
                :label="$t('components.campCreate.campCreateStep2.prototypeCampUrl')"
                class="flex-grow-1"
                @input="setClipboardEntityUrl"
              />
              <ClipboardInfoDialog
                v-if="showClipboardPrompt"
                ref="clipboardInfoDialog"
                translation-context-i18n-key="components.campCreate.campCreateStep2.clipboardInfoDialog"
                @closed="attemptLoadingEntityFromClipboard"
              >
                <template #activator="{ on }">
                  <v-btn
                    :title="$t('components.campCreate.campCreateStep2.pasteCamp')"
                    text
                    class="v-btn--has-bg"
                    height="56"
                    v-on="on"
                  >
                    <v-progress-circular v-if="clipboardEntityLoading" indeterminate />
                    <v-icon v-else>mdi-content-paste</v-icon>
                  </v-btn>
                </template>
              </ClipboardInfoDialog>
              <v-btn
                v-else-if="!clipboardAccessDenied"
                ref="pasteButton"
                :title="$t('components.campCreate.campCreateStep2.pasteCamp')"
                text
                class="v-btn--has-bg"
                height="56"
                @click="attemptLoadingEntityFromClipboard"
              >
                <v-progress-circular v-if="clipboardEntityLoading" indeterminate />
                <v-icon v-else>mdi-content-paste</v-icon>
              </v-btn>
            </div>
            <div v-if="prototypePreview" class="px-2 rounded-lg dashborder">
              <h3 class="mt-2 h3">
                {{ $t('components.campCreate.campCreateStep2.preview') }}
              </h3>
              <v-list class="w-100" dense color="transparent">
                <v-subheader class="px-0" style="height: auto">
                  {{ $t('components.campCreate.campCreateStep2.category') }}
                </v-subheader>
                <v-list-item
                  v-for="category in prototypePreview.categories().items"
                  :key="category._meta.self"
                  class="pt-0 pb-1 px-0 min-h-0"
                >
                  <v-list-item-title class="d-flex gap-2 align-baseline">
                    <CategoryChip :category="category" class="mx-0 flex-shrink-0" dense />
                    <span class="font-weight-medium">{{ category.name }}</span>
                    <small class="blue-grey--text">{{
                      category
                        .preferredContentTypes()
                        .items.map((item) =>
                          $t('contentNode.' + camelCase(item.name) + '.name')
                        )
                        .join(', ') ||
                      $t('components.campCreate.campCreateStep2.noContent')
                    }}</small>
                  </v-list-item-title>
                </v-list-item>
              </v-list>
              <div class="d-flex flex-row">
                <v-list class="w-100 sm:w-50" dense color="transparent">
                  <v-subheader class="px-0" style="height: auto">
                    {{ $t('components.campCreate.campCreateStep2.progressLabels') }}
                  </v-subheader>
                  <v-list-item
                    v-for="(progressLabel, idx) in prototypePreview.progressLabels()
                      .items"
                    :key="progressLabel._meta.self"
                    class="pt-1 pb-1 px-0 min-h-0"
                  >
                    <v-list-item-title class="d-flex gap-2 align-baseline">
                      <v-avatar color="rgba(0,0,0,0.12)" size="20">{{
                        idx + 1
                      }}</v-avatar>
                      {{ progressLabel.title }}
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
                <v-list class="w-100 sm:w-50" dense color="transparent">
                  <v-subheader class="px-0" style="height: auto">
                    {{ $t('components.campCreate.campCreateStep2.materialLists') }}
                  </v-subheader>
                  <v-list-item
                    v-for="materialList in copyableMaterialLists"
                    :key="materialList._meta.self"
                    class="pt-1 pb-1 px-0 min-h-0"
                  >
                    <v-list-item-title class="d-flex gap-2 align-baseline">
                      {{ materialList.name }}
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </div>
              <v-list
                v-if="prototypePreview.checklists().items.length"
                class="w-100"
                dense
                color="transparent"
              >
                <v-subheader class="px-0" style="height: auto">
                  {{ $t('components.campCreate.campCreateStep2.checklists') }}
                </v-subheader>
                <v-list-item
                  v-for="checklist in prototypePreview.checklists().items"
                  :key="checklist._meta.self"
                  class="pt-1 pb-1 px-0 min-h-0"
                >
                  <v-list-item-title class="d-flex gap-2 align-baseline">
                    {{ checklist.name }}
                    <small class="blue-grey--text">{{
                      $t(
                        'components.campCreate.campCreateStep2.checklistItemCount',
                        checklist.checklistItems().items.length
                      )
                    }}</small>
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </div>
            <v-alert
              v-if="localCamp.campPrototype === 'none'"
              color="#0661ab"
              elevation="0"
              text
              icon="mdi-alert-circle-outline"
            >
              <strong>{{
                $t('components.campCreate.campCreateStep2.noPrototypeAlert.title')
              }}</strong>
              <br />
              {{
                $t('components.campCreate.campCreateStep2.noPrototypeAlert.description')
              }}
            </v-alert>
          </v-card-text>
          <v-divider />
          <ContentActions>
            <v-btn
              text
              color="secondary"
              :disabled="isSaving"
              @click="$emit('previous-step')"
            >
              <v-icon left>mdi-arrow-left</v-icon>
              {{ $t('global.button.back') }}
            </v-btn>
            <div class="ml-auto">
              <ButtonCancel :disabled="isSaving" @click="$router.go(-1)" />
              <ButtonAdd
                v-if="valid"
                type="submit"
                :loading="isSaving"
                data-testid="create-camp-button"
              >
                {{ $t('components.campCreate.campCreateStep2.create') }}
              </ButtonAdd>
              <v-tooltip v-else top>
                <template #activator="{ attrs, on }">
                  <ButtonAdd
                    color="secondary"
                    elevation="0"
                    v-bind="attrs"
                    @click="validate()"
                    v-on="on"
                  >
                    {{ $t('components.campCreate.campCreateStep2.create') }}
                  </ButtonAdd>
                </template>
                {{ $t('components.campCreate.campCreateStep2.submitTooltipPrototype') }}
              </v-tooltip>
            </div>
          </ContentActions>
        </v-form>
      </e-form>
    </ValidationObserver>
  </v-stepper-content>
</template>
<script>
import camelCase from 'lodash-es/camelCase.js'
import { ValidationObserver } from 'vee-validate'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import ButtonCancel from '@/components/buttons/ButtonCancel.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import ContentActions from '@/components/layout/ContentActions.vue'
import ServerError from '@/components/form/ServerError.vue'
import ClipboardInfoDialog from '@/components/generic/ClipboardInfoDialog.vue'
import { useClipboardEntity } from '@/components/generic/useClipboardEntity.js'
import router from '@/router.js'
import { apiStore as api } from '@/plugins/store/index.js'
import { reactive, ref, watchEffect } from 'vue'

export default {
  name: 'CampCreateStep2',
  components: {
    ClipboardInfoDialog,
    ButtonAdd,
    ButtonCancel,
    CategoryChip,
    ContentActions,
    ServerError,
    // ValidationObserver,
  },
  props: {
    camp: { type: Object, required: true },
    isSaving: { type: Boolean, required: true },
    serverError: {
      type: [Object, String, Error],
      default: null,
    },
  },
  setup({ camp }) {
    const localCamp = reactive(camp)
    const copyCampUrl = ref('')
    const selectedPrototypeValue = ref(null)
    watchEffect(() => {
      if (['other', 'none', null].includes(selectedPrototypeValue.value)) {
        localCamp.campPrototype = null
      } else {
        localCamp.campPrototype = selectedPrototypeValue.value
      }
    })

    const clipboard = useClipboardEntity({
      fetchClipboardEntity: async (url) => {
        if (!url.startsWith(window.location.origin)) return null
        url = url.substring(window.location.origin.length)
        const match = router.matcher.match(url)
        if (!match.params?.campId) return null

        return await api.get().camps({ id: match.params.campId })
      },
      onEntityLoaded(entity) {
        selectedPrototypeValue.value = entity._meta.self
      },
      onEntityLoadFailed() {
        // If "other" is selected, leave the selection as it is, so the user can try again
        if (selectedPrototypeValue.value !== 'other') selectedPrototypeValue.value = ''
      },
    })

    const setClipboardEntityUrl = (url) => {
      clipboard.setClipboardEntityUrl(url)
      copyCampUrl.value = ''
    }

    return {
      ...clipboard,
      setClipboardEntityUrl,
      localCamp,
      copyCampUrl,
      selectedPrototypeValue,
    }
  },
  computed: {
    campTemplates() {
      return this.campPrototypes.concat([
        ...(this.hasClipboardEntity
          ? [
              {
                value: this.clipboardEntity._meta.self,
                text: this.clipboardEntity.title,
              },
            ]
          : []),
        {
          value: 'other',
          text: this.$t('components.campCreate.campCreateStep2.otherPrototype'),
        },
        {
          value: 'none',
          text: this.$t('components.campCreate.campCreateStep2.noPrototype'),
        },
      ])
    },
    prototypeHint() {
      const campPrototypeUris = this.campPrototypes.map((prototype) => prototype.value)
      switch (this.selectedPrototypeValue) {
        case '':
          return this.$t('components.campCreate.campCreateStep2.prototypeHint')
        case 'none':
          return this.$t('components.campCreate.campCreateStep2.prototypeHintEmpty')
        case 'other':
          return ''
        default:
          if (!campPrototypeUris.includes(this.selectedPrototypeValue)) {
            return this.$t('components.campCreate.campCreateStep2.prototypeHintOther')
          }
          return this.$t('components.campCreate.campCreateStep2.prototypeHintSelected')
      }
    },
    prototypePreview() {
      if (
        this.selectedPrototypeValue === 'none' ||
        this.selectedPrototypeValue === 'other'
      ) {
        return null
      }
      if (this.localCamp?.campPrototype) {
        return this.api.get(this.localCamp.campPrototype)
      }
      return null
    },
    campPrototypes() {
      return this.api
        .get()
        .camps({ isPrototype: true })
        .items.map((ct) => ({
          value: ct._meta.self,
          text: this.$t(ct.title),
          object: ct,
        }))
    },
    copyableMaterialLists() {
      if (!this.prototypePreview) return []
      return this.prototypePreview.materialLists().items.filter((materialList) => {
        return materialList.campCollaboration === null
      })
    },
  },
  watch: {
    selectedPrototypeValue(newPrototype) {
      if (newPrototype === 'other') {
        this.$nextTick(() => {
          if (this.$refs.clipboardInfoDialog) {
            this.$refs.clipboardInfoDialog.setOpen(true)
          } else if (this.clipboardAccessDenied) {
            this.$refs.copyCampUrlInput.focus()
          }
        })
      }
    },
  },
  mounted() {
    this.attemptLoadingEntityFromClipboard().then(() => {
      this.localCamp.campPrototype = ''
    })
  },
  methods: { camelCase },
}
</script>

<style scoped>
.dashborder {
  border: 2px dashed rgba(0, 0, 0, 0.15);
}
</style>
