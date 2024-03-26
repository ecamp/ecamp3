<template>
  <v-stepper-content :step="1" class="pa-0">
    <ValidationObserver v-slot="{ handleSubmit, valid, validate }">
      <e-form name="camp">
        <v-form ref="form" @submit.prevent="handleSubmit(() => $emit('next-step'))">
          <v-card-text>
            <e-text-field
              v-model="localCamp.name"
              path="name"
              vee-rules="required"
              required
              autofocus
            />
            <e-text-field
              v-model="localCamp.title"
              path="title"
              vee-rules="required"
              required
            />
            <e-text-field v-model="localCamp.motto" path="motto" />
            <CreateCampPeriods
              :add-period="addPeriod"
              :periods="localCamp.periods"
              :delete-period="deletePeriod"
              :period-deletable="periodDeletable"
            />
          </v-card-text>
          <v-divider />
          <ContentActions>
            <v-spacer />
            <ButtonCancel :disabled="isSaving" @click="$router.go(-1)" />
            <ButtonContinue v-if="valid" @click="$emit('next-step')" />
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
              {{ $tc('components.campCreate.campCreateStep1.submitTooltip') }}
            </v-tooltip>
          </ContentActions>
        </v-form>
      </e-form>
    </ValidationObserver>
  </v-stepper-content>
</template>
<script>
import { ValidationObserver } from 'vee-validate'
import ButtonCancel from '@/components/buttons/ButtonCancel.vue'
import ButtonContinue from '@/components/buttons/ButtonContinue.vue'
import ContentActions from '@/components/layout/ContentActions.vue'
import CreateCampPeriods from '@/components/campAdmin/CreateCampPeriods.vue'
import ETextField from '@/components/form/base/ETextField.vue'

export default {
  name: 'CampCreateStep1',
  components: {
    ButtonCancel,
    ButtonContinue,
    ContentActions,
    CreateCampPeriods,
    ETextField,
    ValidationObserver,
  },
  props: {
    camp: { type: Object, required: true },
    isSaving: { type: Boolean, required: true },
  },
  data: function () {
    return {
      localCamp: this.camp,
    }
  },
  computed: {
    periodDeletable() {
      return this.camp.periods.length > 1
    },
  },
  methods: {
    addPeriod: function () {
      this.$emit('add-period')
    },
    deletePeriod: function (idx) {
      this.$emit('delete-period', idx)
    },
  },
}
</script>
