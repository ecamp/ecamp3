<template>
  <v-stepper v-model="step" flat>
    <v-stepper-header class="elevation-0">
      <v-spacer v-if="$vuetify.breakpoint.smAndUp" />
      <v-stepper-step :complete="step > 1" :step="1" class="px-4">
        {{ $tc('components.campCreate.campCreate.steps.infos') }}
      </v-stepper-step>
      <v-divider class="mx-n2" />
      <v-stepper-step :complete="step > 2" :step="2" class="px-4">
        {{ $tc('components.campCreate.campCreate.steps.template') }}
      </v-stepper-step>
      <v-spacer v-if="$vuetify.breakpoint.smAndUp" />
    </v-stepper-header>
    <v-divider />
    <v-stepper-items>
      <CampCreateStep1
        :camp="camp"
        :is-saving="isSaving"
        @add-period="addPeriod"
        @delete-period="deletePeriod"
        @next-step="step++"
      />
      <CampCreateStep2
        :camp="camp"
        :is-saving="isSaving"
        :server-error="serverError"
        @create-camp="createCamp"
        @previous-step="step--"
      />
    </v-stepper-items>
  </v-stepper>
</template>
<script>
import { camelCase } from 'lodash'
import { campRoute } from '@/router.js'
import CampCreateStep1 from '@/components/campCreate/CampCreateStep1.vue'
import CampCreateStep2 from '@/components/campCreate/CampCreateStep2.vue'

export default {
  name: 'CampCreate',
  components: { CampCreateStep1, CampCreateStep2 },
  data() {
    return {
      step: 1,
      camp: {
        title: '',
        organizer: '',
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
