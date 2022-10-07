<template>
  <div class="e-form-container">
    <v-card
      v-for="(period, i) in periods"
      :key="period.key"
      outlined
      color="grey lighten-3"
      class="period mb-2 rounded-b-0"
    >
      <v-row no-gutters>
        <v-col>
          <legend class="pa-2">
            {{ $tc('entity.period.name') }}
          </legend>
        </v-col>
        <v-col cols="auto">
          <v-btn
            class="ml-2 px-2"
            text
            min-width="auto"
            color="error"
            :disabled="!periodDeletable"
            @click="deletePeriod(i)"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-col>
      </v-row>
      <v-row no-gutters class="mx-2">
        <v-col>
          <e-text-field
            v-model="period.description"
            :label="$tc('entity.period.fields.description')"
            single-line
            :name="$tc('entity.period.fields.description')"
            :filled="false"
            vee-rules="required"
            :my="false"
            input-class="mb-2 pt-0"
            required
          />
        </v-col>
      </v-row>
      <v-row no-gutters class="mx-2 mb-2">
        <v-col>
          <e-date-picker
            v-model="period.start"
            :name="$tc('entity.period.fields.start')"
            vee-id="start"
            vee-rules="required"
            :my="2"
            :filled="false"
            required
          />
        </v-col>
        <v-col>
          <e-date-picker
            v-model="period.end"
            input-class="ml-2"
            :name="$tc('entity.period.fields.end')"
            vee-rules="required|greaterThanOrEqual_date:@start"
            :my="2"
            :filled="false"
            required
          />
        </v-col>
      </v-row>
    </v-card>
    <v-btn text block height="auto" class="pa-4" @click="addPeriod">
      <v-icon>mdi-plus</v-icon>
      {{ $tc('views.campCreate.period.add') }}
    </v-btn>
  </div>
</template>
<script>
import EDatePicker from '@/components/form/base/EDatePicker.vue'
import ETextField from '@/components/form/base/ETextField.vue'

export default {
  name: 'CreateCampPeriods',
  components: {
    EDatePicker,
    ETextField,
  },
  props: {
    addPeriod: {
      type: Function,
      required: true,
    },
    periods: {
      type: Array,
      required: true,
    },
    deletePeriod: {
      type: Function,
      required: true,
    },
    periodDeletable: {
      type: Boolean,
      required: true,
    },
  },
}
</script>
<style scoped lang="scss">
.period.period {
  border-bottom-width: 1px !important;
  border-bottom-style: solid !important;
  border-bottom-color: rgba(0, 0, 0, 0.42) !important;
}
</style>
