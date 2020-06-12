<template>
  <v-container fluid>
    <content-card max-width="800">
      <v-toolbar>
        <v-toolbar-title>
          <ButtonBack />
          {{ $t('camp.create') }}
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-form ref="form" @submit.prevent="createCamp">
          <e-text-field
            v-model="camp.name"
            :label="$t('camp.name')"
            required
            autofocus />
          <e-text-field
            v-model="camp.title"
            :label="$t('camp.title')"
            required />
          <e-text-field
            v-model="camp.motto"
            :label="$t('camp.motto')"
            required />
          <e-select
            v-model="camp.campTypeId"
            :label="$t('camp.campType')"
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
          <v-list>
            <v-list-item>
              <v-list-item-title>
                {{ $t('camp.periods') }}
              </v-list-item-title>
              <v-list-item-action>
                <v-btn @click="addPeriod">
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </v-list-item-action>
            </v-list-item>
            <div v-for="(period, i) in camp.periods" :key="period.key">
              <v-list-item>
                <v-list-item-content>
                  <e-text-field
                    v-model="period.description"
                    :label="$t('period.description')"
                    required />
                </v-list-item-content>
                <v-list-item-action>
                  <v-btn :disabled="!periodDeletable" @click="deletePeriod(i)">
                    <v-icon>mdi-delete</v-icon>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
              <v-list-item>
                <v-list-item-content>
                  <e-date-picker
                    v-model="period.start"
                    :label="$t('period.start')"
                    required />
                  <e-date-picker
                    v-model="period.end"
                    :label="$t('period.end')"
                    required />
                </v-list-item-content>
              </v-list-item>
              <v-divider />
            </div>
          </v-list>
          <div class="text-right">
            <ButtonAdd type="submit">
              {{ $t('camp.create') }}
            </ButtonAdd>
          </div>
        </v-form>
      </v-card-text>
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

export default {
  name: 'Camps',
  components: {
    ButtonBack,
    ButtonAdd,
    ContentCard,
    EDatePicker,
    ETextField,
    ESelect
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
