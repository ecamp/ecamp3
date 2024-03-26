<template>
  <v-stepper-content :step="2" class="pa-0">
    <ValidationObserver v-slot="{ handleSubmit, valid, validate }">
      <e-form name="camp">
        <v-form ref="form" @submit.prevent="handleSubmit(() => $emit('create-camp'))">
          <v-card-text>
            <server-error :server-error="serverError" />
            <e-select
              v-model="localCamp.campPrototype"
              :vee-rules="{ required: false, excluded: ['', false, true] }"
              :skip-if-empty="false"
              :label="$tc('entity.camp.prototype')"
              :hint="prorotypeHint"
              persistent-hint
              :items="campTemplates"
              :menu-props="{ offsetY: true }"
            />
            <div v-if="localCamp.campPrototype" class="px-2 rounded-lg dashborder">
              <h3 class="mt-2 h3">
                {{ $tc('components.campCreate.campCreateStep2.preview') }}
              </h3>
              <v-list class="w-100" dense color="transparent">
                <v-subheader class="px-0" style="height: auto">
                  {{ $tc('components.campCreate.campCreateStep2.category') }}
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
                          $tc('contentNode.' + camelCase(item.name) + '.name')
                        )
                        .join(', ') ||
                      $tc('components.campCreate.campCreateStep2.noContent')
                    }}</small>
                  </v-list-item-title>
                </v-list-item>
              </v-list>
              <v-list class="w-100" dense color="transparent">
                <v-subheader class="px-0" style="height: auto">
                  {{ $tc('components.campCreate.campCreateStep2.progressStates') }}
                </v-subheader>
                <v-list-item
                  v-for="(progressLabel, idx) in prototypePreview.progressLabels().items"
                  :key="progressLabel._meta.self"
                  class="pt-1 pb-1 px-0 min-h-0"
                >
                  <v-list-item-title class="d-flex gap-2 align-baseline">
                    <v-avatar color="rgba(0,0,0,0.12)" size="20">{{ idx + 1 }}</v-avatar>
                    {{ progressLabel.title }}
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </div>
            <v-alert
              v-if="localCamp.campPrototype === null"
              color="#0661ab"
              elevation="0"
              text
              icon="mdi-alert-circle-outline"
            >
              <strong>{{
                $tc('components.campCreate.campCreateStep2.noPrototypeAlert.title')
              }}</strong>
              <br />
              {{
                $tc('components.campCreate.campCreateStep2.noPrototypeAlert.description')
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
              {{ $tc('global.button.back') }}
            </v-btn>
            <div class="ml-auto">
              <ButtonCancel :disabled="isSaving" @click="$router.go(-1)" />
              <ButtonAdd v-if="valid" type="submit" :loading="isSaving">
                {{ $tc('components.campCreate.campCreateStep2.create') }}
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
                    {{ $tc('components.campCreate.campCreateStep2.create') }}
                  </ButtonAdd>
                </template>
                {{ $tc('components.campCreate.campCreateStep2.submitTooltipPrototype') }}
              </v-tooltip>
            </div>
          </ContentActions>
        </v-form>
      </e-form>
    </ValidationObserver>
  </v-stepper-content>
</template>
<script>
import camelCase from 'lodash/camelCase.js'
import { ValidationObserver } from 'vee-validate'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import ButtonCancel from '@/components/buttons/ButtonCancel.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import ContentActions from '@/components/layout/ContentActions.vue'
import ServerError from '@/components/form/ServerError.vue'

export default {
  name: 'CampCreateStep2',
  components: {
    ButtonAdd,
    ButtonCancel,
    CategoryChip,
    ContentActions,
    ServerError,
    ValidationObserver,
  },
  props: {
    camp: { type: Object, required: true },
    isSaving: { type: Boolean, required: true },
    serverError: {
      type: [Object, String, Error],
      default: null,
    },
  },
  data: function () {
    return {
      localCamp: this.camp,
    }
  },
  computed: {
    campTemplates() {
      return this.campPrototypes.concat([
        {
          value: null,
          text: this.$tc('components.campCreate.campCreateStep2.noPrototype'),
        },
      ])
    },
    prorotypeHint() {
      switch (this.localCamp.campPrototype) {
        case '':
          return this.$tc('components.campCreate.campCreateStep2.prototypeHint')
        case null:
          return this.$tc('components.campCreate.campCreateStep2.prototypeHintEmpty')
        default:
          return this.$tc('components.campCreate.campCreateStep2.prototypeHintSelected')
      }
    },
    prototypePreview() {
      if (this.localCamp.campPrototype) {
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
          text: this.$tc(ct.name),
          object: ct,
        }))
    },
  },
  methods: { camelCase },
}
</script>

<style scoped>
.dashborder {
  border: 2px dashed rgba(0, 0, 0, 0.15);
}
</style>
