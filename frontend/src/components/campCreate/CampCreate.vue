<template>
  <v-stepper :model-value="step" flat>
    <v-stepper-header class="elevation-0">
      <v-spacer v-if="$vuetify.display.smAndUp" class="w-100" />
      <v-stepper-item :value="1" :complete="step > 1" class="px-4" color="primary">
        {{ $t('components.campCreate.campCreate.steps.infos') }}
      </v-stepper-item>
      <v-divider class="mx-n2" />
      <v-stepper-item :value="2" :complete="step > 2" class="px-4" color="primary">
        {{ $t('components.campCreate.campCreate.steps.template') }}
      </v-stepper-item>
      <v-spacer v-if="$vuetify.display.smAndUp" class="w-100" />
    </v-stepper-header>
    <v-divider />
    <v-stepper-window class="ma-0">
      <v-stepper-window-item :value="1">
        <CampCreateStep1
          :camp="camp"
          :is-saving="isSaving"
          @add-period="addPeriod"
          @delete-period="deletePeriod"
          @next-step="step++"
        />
      </v-stepper-window-item>
      <v-stepper-window-item :value="2">
        <CampCreateStep2
          :camp="camp"
          :is-saving="isSaving"
          :server-error="serverError"
          @create-camp="createCamp"
          @previous-step="step--"
        />
      </v-stepper-window-item>
    </v-stepper-window>
  </v-stepper>
</template>
<script>
import { camelCase } from 'lodash-es'
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
            description: this.$t('entity.period.defaultDescription'),
          },
        ],
        campPrototype: null,
      },
      serverError: null,
      isSaving: false,
    }
  },
  created() {},
  methods: {
    camelCase,
    createCamp: async function () {
      this.isSaving = true

      try {
        const campsUrl = await this.api.href(this.api.get(), 'camps')
        const camp = await this.api.post(campsUrl, this.camp)
        await this.$router.push(campRoute(camp, 'admin'))
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
