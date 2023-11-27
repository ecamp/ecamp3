<template>
  <v-container fluid>
    <content-card max-width="800" :title="$tc('views.campCreate.title')" toolbar>
      <v-stepper v-model="step" flat>
        <v-stepper-header class="elevation-0">
          <v-stepper-step :complete="step > 1" :step="1" class="px-4">
            Infos
          </v-stepper-step>
          <v-divider class="mx-n2" />
          <v-stepper-step :complete="step > 2" :step="2" class="px-4">
            Vorlage
          </v-stepper-step>
          <v-divider class="mx-n2" />
          <v-stepper-step :complete="step > 3" :step="3" class="px-4">
            Konfigurieren
          </v-stepper-step>
        </v-stepper-header>
        <v-divider />
        <v-stepper-items>
          <v-stepper-content :step="1" class="pa-0">
            <ValidationObserver v-slot="{ handleSubmit, valid, validate }">
              <v-form ref="form" @submit.prevent="handleSubmit(() => step++)">
                <v-card-text>
                  <e-text-field
                    v-model="camp.name"
                    :name="$tc('entity.camp.fields.name')"
                    vee-rules="required"
                    required
                    autofocus
                  />
                  <e-text-field
                    v-model="camp.title"
                    :name="$tc('entity.camp.fields.title')"
                    vee-rules="required"
                    required
                  />
                  <e-text-field
                    v-model="camp.motto"
                    :name="$tc('entity.camp.fields.motto')"
                  />
                  <create-camp-periods
                    :add-period="addPeriod"
                    :periods="camp.periods"
                    :delete-period="deletePeriod"
                    :period-deletable="periodDeletable"
                  />
                </v-card-text>
                <v-divider />
                <ContentActions>
                  <v-spacer />
                  <ButtonCancel :disabled="isSaving" @click="$router.go(-1)" />
                  <ButtonContinue v-if="valid" @click="step++" />
                  <v-tooltip v-else top>
                    <template #activator="{ attrs, on }">
                      <v-btn
                        elevation="0"
                        color="secondary"
                        v-bind="attrs"
                        @click="validate()"
                        v-on="on"
                      >
                        {{ $tc('global.button.continue') }}
                      </v-btn>
                    </template>
                    Bitte fülle alle Pflichtfelder aus.
                  </v-tooltip>
                </ContentActions>
              </v-form>
            </ValidationObserver>
          </v-stepper-content>
          <v-stepper-content :step="2" class="pa-0">
            <ValidationObserver v-slot="{ handleSubmit, valid, validate }">
              <v-form ref="form" @submit.prevent="handleSubmit(createCamp)">
                <v-card-text>
                  <server-error :server-error="serverError" />
                  <e-select
                    v-model="camp.campPrototype"
                    :vee-rules="{ required: false, excluded: ['', false, true] }"
                    :skip-if-empty="false"
                    :name="$tc('entity.camp.prototype')"
                    :hint="prorotypeHint"
                    persistent-hint
                    :items="campTemplates"
                  >
                    <template #item="data">
                      <v-list-item v-bind="data.attrs" v-on="data.on">
                        <v-list-item-content>
                          {{ data.item.text }}
                        </v-list-item-content>
                      </v-list-item>
                    </template>
                  </e-select>
                </v-card-text>
                <v-sheet v-if="camp.campPrototype" elevation="0">
                  <v-expansion-panels
                    accordion
                    flat
                    multiple
                    :value="[0]"
                    style="border-top: 1px solid #eee"
                  >
                    <v-expansion-panel>
                      <v-expansion-panel-header>
                        <h3>Activity category preview</h3>
                      </v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-list class="py-0 mx-n4">
                          <v-list-item
                            v-for="category in prototypePreview.categories().items"
                            :key="category._meta.self"
                          >
                            <v-list-item-title class="d-flex gap-2 align-baseline">
                              <CategoryChip
                                :category="category"
                                class="mx-1 flex-shrink-0"
                                dense
                              />
                              <span class="font-weight-medium">{{ category.name }}</span>
                              <small class="blue-grey--text">{{
                                category
                                  .preferredContentTypes()
                                  .items.map((item) =>
                                    $tc('contentNode.' + camelCase(item.name) + '.name')
                                  )
                                  .join(', ') || $tc('views.campCreate.noContent')
                              }}</small>
                            </v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>
                        <h3>Activity states preview</h3>
                      </v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-list class="py-0 mx-n4">
                          <v-list-item
                            v-for="(
                              progressLabel, idx
                            ) in prototypePreview.progressLabels().items"
                            :key="progressLabel._meta.self"
                          >
                            <v-list-item-title class="d-flex gap-2 align-baseline">
                              <v-avatar color="grey lighten-2 subtitle-2" size="24"
                                >{{ idx + 1 }}
                              </v-avatar>
                              {{ progressLabel.title }}
                            </v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-sheet>
                <v-divider v-else />
                <ContentActions>
                  <v-btn text color="secondary" :disabled="isSaving" @click="step--">
                    <v-icon left>mdi-arrow-left</v-icon>
                    {{ $tc('global.button.back') }}
                  </v-btn>
                  <v-spacer />
                  <ButtonCancel :disabled="isSaving" @click="$router.go(-1)" />
                  <ButtonAdd v-if="valid" type="submit" :loading="isSaving">
                    {{ $tc('views.campCreate.create') }}
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
                        {{ $tc('views.campCreate.create') }}
                      </ButtonAdd>
                    </template>
                    Du must noch auswählen ob und welche Lagervorlage du verwenden
                    möchtest.
                  </v-tooltip>
                </ContentActions>
              </v-form>
            </ValidationObserver>
          </v-stepper-content>
        </v-stepper-items>
      </v-stepper>
    </content-card>
  </v-container>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import ButtonCancel from '@/components/buttons/ButtonCancel.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import ETextField from '@/components/form/base/ETextField.vue'
import { campRoute } from '@/router.js'
import ServerError from '@/components/form/ServerError.vue'
import { ValidationObserver } from 'vee-validate'
import CreateCampPeriods from '@/components/campAdmin/CreateCampPeriods.vue'
import ButtonContinue from '@/components/buttons/ButtonContinue.vue'
import ContentActions from '@/components/layout/ContentActions.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import { camelCase } from 'lodash'

export default {
  name: 'Camps',
  components: {
    CategoryChip,
    ContentActions,
    ButtonContinue,
    CreateCampPeriods,
    ButtonAdd,
    ButtonCancel,
    ContentCard,
    ETextField,
    ValidationObserver,
    ServerError,
  },
  data() {
    return {
      step: 1,
      camp: {
        name: '',
        title: '',
        motto: '',
        periods: [
          {
            start: '',
            end: '',
            description: this.$tc('entity.period.defaultDescription'),
          },
        ],
        campPrototype: '',
      },
      serverError: null,
      isSaving: false,
    }
  },
  computed: {
    campTemplates() {
      return [
        {
          value: null,
          text: this.$tc('views.campCreate.noPrototype'),
        },
      ].concat(this.campPrototypes)
    },
    prorotypeHint() {
      switch (this.camp.campPrototype) {
        case '':
          return this.$tc('views.campCreate.prototypeHint')
        case null:
          return this.$tc('views.campCreate.prototypeHintEmpty')
        default:
          return this.$tc('views.campCreate.prototypeHintSelected')
      }
    },
    prototypePreview() {
      if (this.camp.campPrototype) {
        return this.api.get(this.camp.campPrototype)
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
    periodDeletable() {
      return this.camp.periods.length > 1
    },
    campsUrl() {
      return this.api.get().camps()._meta.self
    },
  },
  created() {},
  methods: {
    camelCase,
    createCamp: async function () {
      this.isSaving = true

      try {
        const camp = await this.api.post(this.campsUrl, this.camp)
        await this.$router.push(campRoute(camp, 'admin'))
        this.api.reload(this.campsUrl)
      } catch (error) {
        this.serverError = error
      }

      this.isSaving = false
    },
    addPeriod: function () {
      this.camp.periods.push({
        start: '',
        end: '',
        description: '',
      })
    },
    deletePeriod: function (idx) {
      this.camp.periods.splice(idx, 1)
    },
  },
}
</script>

<style scoped></style>
