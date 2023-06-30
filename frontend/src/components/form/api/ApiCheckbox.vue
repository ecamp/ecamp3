<!--
Displays a field as a e-checkbox + write access via API wrapper

:loading doesn't work yet: https://github.com/vuetifyjs/vuetify/issues/7843
-->

<template>
  <api-wrapper v-slot="wrapper" v-bind="$props" separate-buttons v-on="$listeners">
    <e-checkbox
      :value="wrapper.localValue"
      v-bind="$attrs"
      :readonly="wrapper.readonly"
      :disabled="disabled"
      :error-messages="wrapper.errorMessages"
      :loading="wrapper.isSaving"
      @input="wrapper.on.input"
    >
      <template #append>
        <ApiWrapperCheckboxAppend
          v-if="
            wrapper.hasServerError ||
            (wrapper.autoSave && wrapper.dirty) ||
            wrapper.hasLoadingError
          "
          :wrapper="wrapper"
        />
      </template>
    </e-checkbox>
  </api-wrapper>
</template>

<script>
import { apiPropsMixin } from '@/mixins/apiPropsMixin.js'
import ApiWrapper from './ApiWrapper.vue'
import ApiWrapperCheckboxAppend from '@/components/form/api/ApiWrapperCheckboxAppend.vue'

export default {
  name: 'ApiCheckbox',
  components: { ApiWrapperCheckboxAppend, ApiWrapper },
  mixins: [apiPropsMixin],
  props: {
    // disable delay per default
    autoSaveDelay: { type: Number, default: 0, required: false },
  },
  data() {
    return {}
  },
}
</script>

<style scoped lang="scss">
.ec-api-wrapper--saving ::v-deep .v-input--selection-controls__input .v-icon {
  transition: color 0.2s ease;
  color: rgba(0, 0, 0, 0.5) !important;
}

.ec-api-wrapper ::v-deep .v-input--checkbox .v-input--selection-controls__input::before {
  border: 2px solid transparent;
  border-radius: 50%;
  position: absolute;
  height: 24px;
  width: 24px;
  content: '\F0131';
  color: transparent;
  transition: transform 0.2s ease-out, border-color 0.2s ease;
  transform: scale(1.9);
  pointer-events: none;
}

.ec-api-wrapper--saving
  ::v-deep
  .v-input--checkbox
  .v-input--selection-controls__input::before {
  border-color: map-get($blue, 'base');
  -webkit-mask: conic-gradient(from 0deg at 50% 50%, #0000 0%, #000);
  transform: scale(1.2);
  animation: spin 1s linear infinite both;
  transition: transform 0.2s ease-out;
}

@keyframes spin {
  from {
    transform: scale(1.5) rotate(0deg);
  }
  to {
    transform: scale(1.5) rotate(360deg);
  }
}

.ec-api-wrapper--success
  ::v-deep
  .v-input--checkbox
  .v-input--selection-controls__input::before {
  border-color: map-get($green, 'lighten-2');
  transform: scale(1.5);
  transition: none;
}

.ec-api-wrapper--server-error
  ::v-deep
  .v-input--checkbox
  .v-input--selection-controls__input::before {
  transform: scale(1.5);
}
</style>
