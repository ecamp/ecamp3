<template>
  <v-container fluid>
    <content-card max-width="800">
      <v-toolbar>
        <v-toolbar-title>
          <ButtonBack />
          {{ $t('camp.create') }}
        </v-toolbar-title>
      </v-toolbar>
      <ValidationObserver v-slot="{ handleSubmit }">
        <v-form ref="form" @submit.prevent="handleSubmit(createCamp)">
          <v-card-text>
            <server-error :server-error="serverError" />
            <e-text-field
              v-model="camp.name"
              :label="$t('camp.name')"
              :name="$t('camp.name')"
              vee-rules="required"
              required
              autofocus />
            <e-text-field
              v-model="camp.title"
              :label="$t('camp.title')"
              :name="$t('camp.title')"
              vee-rules="required"
              required />
            <e-text-field
              v-model="camp.motto"
              :label="$t('camp.motto')"
              :name="$t('camp.motto')"
              vee-rules="required"
              required />
            <e-select
              v-model="camp.campTypeId"
              :label="$t('camp.campType')"
              :name="$t('camp.campType')"
              vee-rules="required"
              :items="campTypes">
              <template v-slot:item="data">
                <v-list-item v-bind="data.attrs" v-on="data.on">
                  <v-list-item-content>
                    {{ data.item.text }}
                  </v-list-item-content>
                  <v-list-item-action-text>
                    <v-icon v-if="data.item.object.isCourse" left>
                      mdi-school
                    </v-icon>
                  </v-list-item-action-text>
                </v-list-item>
              </template>
            </e-select>
            <v-card v-for="(period, i) in camp.periods"
                    :key="period.key"
                    outlined
                    color="grey lighten-3" class="period mb-2 rounded-b-0">
              <v-row no-gutters>
                <v-col>
                  <legend class="pa-2">
                    {{ $t('camp.period.name') }}
                  </legend>
                </v-col>
                <v-col cols="auto">
                  <v-btn
                    class="ml-2 px-2"
                    text min-width="auto"
                    color="error"
                    :disabled="!periodDeletable" @click="deletePeriod(i)">
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                </v-col>
              </v-row>
              <v-row no-gutters class="mx-2">
                <v-col>
                  <e-text-field
                    v-model="period.description"
                    :label="$t('period.description')"
                    single-line
                    :name="$t('period.description')"
                    :filled="false"
                    vee-rules="required"
                    :my="false"
                    input-class="mb-2 pt-0"
                    required />
                </v-col>
              </v-row>
              <v-row no-gutters class="mx-2 mb-2">
                <v-col>
                  <e-date-picker
                    v-model="period.start"
                    :label="$t('period.start')"
                    :name="$t('period.start')"
                    vee-rules="required"
                    :my="2"
                    :filled="false"
                    required />
                </v-col>
                <v-col>
                  <e-date-picker
                    v-model="period.end"
                    input-class="ml-2"
                    :label="$t('period.end')"
                    :name="$t('period.end')"
                    vee-rules="required"
                    :my="2"
                    :filled="false"
                    icon=""
                    required />
                </v-col>
              </v-row>
            </v-card>
            <v-btn text
                   block
                   height="auto" class="pa-4"
                   @click="addPeriod">
              <v-icon>mdi-plus</v-icon>
              {{ $t('camp.period.add') }}
            </v-btn>
          </v-card-text>
          <v-divider />
          <v-card-text class="text-right">
            <ButtonAdd type="submit">
              {{ $t('camp.create') }}
            </ButtonAdd>
          </v-card-text>
        </v-form>
      </ValidationObserver>
    </content-card>
  </v-container>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd'
import ButtonBack from '@/components/buttons/ButtonBack'
import ContentCard from '@/components/layout/ContentCard'
import EDatePicker from '@/components/form/base/EDatePicker'
import ETextField from '@/components/form/base/ETextField'
import ESelect from '@/components/form/base/ESelect'
import { campRoute } from '@/router'
import ServerError from '@/components/form/ServerError'
import { ValidationObserver } from 'vee-validate'

export default {
  name: 'Camps',
  components: {
    ButtonBack,
    ButtonAdd,
    ContentCard,
    EDatePicker,
    ETextField,
    ESelect,
    ValidationObserver,
    ServerError
  },
  data () {
    return {
      camp: {
        name: '',
        title: '',
        motto: '',
        campTypeId: null,
        periods: [
          {
            key: 0,
            start: '',
            end: '',
            description: this.$i18n.t('period.defaultDescription')
          }
        ]
      },
      serverError: null,
      periodKey: 0
    }
  },
  computed: {
    campTypes () {
      return this.api.get().campTypes().items.map(ct => ({
        value: ct.id,
        text: this.$i18n.t(ct.name),
        object: ct
      }))
    },
    periodDeletable () {
      return this.camp.periods.length > 1
    },
    campsUrl () {
      return this.api.get().camps()._meta.self
    }
  },
  created () {
  },
  methods: {
    createCamp: function () {
      this.api.post(this.campsUrl, this.camp).then(c => {
        this.$router.push(campRoute(c, 'admin'))
        this.api.reload(this.campsUrl)
      }, (error) => {
        this.serverError = error
      })
    },
    addPeriod: function () {
      this.camp.periods.push({
        key: ++this.periodKey,
        start: '',
        end: '',
        description: ''
      })
    },
    deletePeriod: function (idx) {
      this.camp.periods.splice(idx, 1)
    }
  }
}
</script>

<style scoped lang="scss">
  .period.period {
    border-bottom-width: 1px !important;
    border-bottom-style: solid !important;
    border-bottom-color: rgba(0, 0, 0, 0.42) !important;
  }
</style>
