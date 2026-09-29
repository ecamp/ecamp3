<template>
  <ValidationForm ref="form" @submit="() => $emit('nextStep')">
    <v-card-text>
      <server-error :server-error="serverError" class="mb-4" />

      <e-autocomplete
        v-if="!hideSearch"
        :model-value="modelValue"
        :items="eventItems"
        :label="$t('components.campImport.campImportStep1.search')"
        :no-data-text="$t('components.campImport.campImportStep1.noEvents')"
        :validation-label-override="$t('components.campImport.campImportStep1.event')"
        :vee-rules="{ required: true }"
        :skip-if-empty="false"
        :error-messages="conflictError ? [conflictError] : []"
        path="event"
        data-testid="import-event-select"
        class="mb-2"
        @update:model-value="$emit('update:modelValue', $event)"
      >
        <template #item-append="{ item }">
          <v-chip
            v-if="item.isImported"
            size="x-small"
            class="align-self-center px-2 v-btn--has-bg"
            data-testid="import-event-already-imported"
          >
            {{ $t('components.campImport.campImportStep1.alreadyImported') }}
          </v-chip>
        </template>
      </e-autocomplete>

      <v-skeleton-loader v-if="isLoadingEvent" type="table" />
      <HitobitoEventSummary v-else-if="camp" :camp="camp" />
    </v-card-text>

    <v-divider />
    <ContentActions>
      <v-spacer />
      <ButtonCancel :disabled="isSaving" @click="$router.push({ name: 'camps' })" />
      <ButtonContinue v-if="camp" data-testid="import-camp-next-step" />
      <v-tooltip v-else location="top">
        <template #activator="{ props }">
          <v-btn color="secondary" variant="flat" v-bind="props" type="submit">
            {{ $t('global.button.continue') }}
          </v-btn>
        </template>
        {{ $t('components.campImport.campImportStep1.submitTooltip') }}
      </v-tooltip>
    </ContentActions>
  </ValidationForm>
</template>

<script>
import { Form as ValidationForm } from 'vee-validate'
import ButtonCancel from '@/components/buttons/ButtonCancel.vue'
import ButtonContinue from '@/components/buttons/ButtonContinue.vue'
import ContentActions from '@/components/layout/ContentActions.vue'
import EAutocomplete from '@/components/form/base/EAutocomplete.vue'
import HitobitoEventSummary from '@/components/campImport/HitobitoEventSummary.vue'
import ServerError from '@/components/form/ServerError.vue'

export default {
  name: 'CampImportStep1',
  components: {
    ButtonCancel,
    ButtonContinue,
    ContentActions,
    EAutocomplete,
    HitobitoEventSummary,
    ServerError,
    ValidationForm,
  },
  props: {
    modelValue: { type: String, default: null },
    events: { type: Array, default: () => [] },
    camp: { type: Object, default: null },
    isLoadingEvent: { type: Boolean, default: false },
    isSaving: { type: Boolean, default: false },
    hideSearch: { type: Boolean, default: false },
    conflictError: { type: String, default: null },
    serverError: { type: [Object, String, Error], default: null },
  },
  emits: ['nextStep', 'update:modelValue'],
  computed: {
    eventItems() {
      return this.events.map((event) => ({
        value: event._meta.self,
        text: event.name,
        isImported: event.isImported,
        props: { disabled: event.isImported },
      }))
    },
  },
}
</script>
