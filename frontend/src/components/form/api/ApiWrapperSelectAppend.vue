<template>
  <div class="ec-api-select-append">
    <!-- Success icon after saving -->
    <div class="ec-api-select-append__icon"></div>

    <div class="d-flex gap-1" style="margin-top: -5px">
      <!-- Retry/Cancel button if saving failed -->
      <template v-if="wrapper.hasServerError">
        <v-tooltip bottom class="ml-auto">
          <template #activator="{ on }">
            <v-btn
              fab
              dark
              depressed
              x-small
              color="error"
              type="submit"
              :aria-label="$tc('global.button.tryagain')"
              v-on="on"
              @click="wrapper.on.save"
            >
              <v-icon>mdi-refresh</v-icon>
            </v-btn>
          </template>
          <span>{{ $tc('global.button.tryagain') }}</span>
        </v-tooltip>
        <v-tooltip v-if="resettable" bottom class="ml-auto">
          <template #activator="{ on }">
            <v-btn
              fab
              dark
              depressed
              x-small
              color="grey"
              :aria-label="$tc('global.button.cancel')"
              v-on="on"
              @click="wrapper.on.reset"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </template>
          <span>{{ $tc('global.button.cancel') }}</span>
        </v-tooltip>
      </template>

      <template v-else-if="!wrapper.autoSave">
        <v-tooltip v-if="wrapper.dirty" bottom class="ml-auto">
          <template #activator="{ on }">
            <v-btn
              fab
              dark
              depressed
              x-small
              color="success"
              type="submit"
              class="mr-1"
              :aria-label="$tc('global.button.save')"
              v-on="on"
            >
              <v-icon>mdi-check</v-icon>
            </v-btn>
          </template>
          <span>{{ $tc('global.button.save') }}</span>
        </v-tooltip>
        <v-tooltip v-if="resettable" bottom class="ml-auto">
          <template #activator="{ on }">
            <v-btn
              fab
              dark
              depressed
              x-small
              color="grey"
              :aria-label="$tc('global.button.cancel')"
              v-on="on"
              @click="wrapper.on.reset"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </template>
          <span>{{ $tc('global.button.cancel') }}</span>
        </v-tooltip>
      </template>

      <!-- Retry button if loading failed -->
      <button-retry v-if="wrapper.hasLoadingError" text @click="wrapper.on.reload" />
    </div>
  </div>
</template>

<script>
import ButtonRetry from '@/components/buttons/ButtonRetry.vue'

export default {
  name: 'ApiWrapperSelectAppend',
  components: { ButtonRetry },
  props: {
    wrapper: {
      required: true,
      type: Object,
    },
    icon: {
      type: String,
      default: 'mdi-cloud-check-variant-outline',
    },
    resettable: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    checkIconAddon() {
      if (this.wrapper.hasServerError || this.wrapper.dirty) {
        return 'hidden'
      } else if (this.wrapper.status === 'success') {
        return 'visible'
      } else {
        return ''
      }
    },
  },
}
</script>

<style lang="scss" scoped>
.ec-api-select-append__icon {
  border-radius: 50%;
  border: 2px solid transparent;
  position: absolute;
  inset: 0px;
  margin-top: 0px;
  margin-left: 4px;
  transform: scale(1.2) rotate(0);
  transition: transform 0.2s ease-out, border-color 0.2s ease;
}

.ec-api-wrapper--saving .ec-api-select-append__icon {
  border-color: map-get($blue, 'darken-2');
  -webkit-mask: conic-gradient(from 0deg at 50% 50%, #0000 0%, #000);
  animation: spin-only 1s linear infinite;
  transform: scale(1) rotate(0);
  transition: transform 0.2s ease-out, border-color 0.2s ease;
}

.ec-api-wrapper--success .ec-api-select-append__icon {
  border-color: map-get($green, 'base');
  transition: transform 0.2s ease-out, border-color 0.2s ease;
  transform: scale(1) rotate(0);
}
</style>
