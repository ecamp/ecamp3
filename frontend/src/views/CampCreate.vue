<template>
  <v-container fluid>
    <content-card max-width="800">
      <v-toolbar>
        <v-toolbar-title>
          <ButtonBack />
          {{ $tc('views.campCreate.title') }}
        </v-toolbar-title>
      </v-toolbar>
      <ValidationObserver v-slot="{ handleSubmit }">
        <v-form ref="form" @submit.prevent="handleSubmit(createCamp)">
          <v-card-text>
            <server-error :server-error="serverError" />
            <e-text-field
              v-model="camp.name"
              :name="$tc('entity.camp.fields.name')"
              vee-rules="required"
              required
              autofocus />
            <e-text-field
              v-model="camp.title"
              :name="$tc('entity.camp.fields.title')"
              vee-rules="required"
              required />
            <e-text-field
              v-model="camp.motto"
              :name="$tc('entity.camp.fields.motto')"
              vee-rules="required"
              required />
            <e-select
              v-model="camp.campTypeId"
              :name="$tc('entity.camp.fields.campType')"
              vee-rules="required"
              :items="campTypeOptions">
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
            <create-camp-periods :add-period="addPeriod" :periods="camp.periods"
                                 :delete-period="deletePeriod" :period-deletable="periodDeletable" />
          </v-card-text>
          <v-divider />
          <v-card-text class="text-right">
            <ButtonAdd type="submit">
              {{ $tc('views.campCreate.create') }}
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
import ETextField from '@/components/form/base/ETextField'
import ESelect from '@/components/form/base/ESelect'
import { campRoute } from '@/router'
import ServerError from '@/components/form/ServerError'
import { ValidationObserver } from 'vee-validate'
import CreateCampPeriods from '@/components/camp/CreateCampPeriods'

export default {
  name: 'Camps',
  components: {
    CreateCampPeriods,
    ButtonBack,
    ButtonAdd,
    ContentCard,
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
            description: this.$tc('entity.period.defaultDescription')
          }
        ]
      },
      serverError: null,
      periodKey: 0
    }
  },
  computed: {
    organizations () {
      return this.api.get().organizations().items
    },
    campTypes () {
      return this.api.get().campTypes().items
    },
    campTypeOptions () {
      const options = []

      this.organizations.forEach(org => {
        if (options.length > 0) {
          options.push({ divider: true })
        }
        options.push({ header: this.$tc(org.name) })

        this.campTypes.forEach(ct => {
          if (ct.organization().id === org.id) {
            options.push({
              value: ct.id,
              text: this.$tc(ct.name),
              object: ct
            })
          }
        })
      })

      return options
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

<style scoped>

</style>
