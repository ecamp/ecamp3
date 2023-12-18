<template>
  <div class="relative">
    <!-- Success icon after saving -->
    <div class="checkIconContainer">
      <v-icon
        v-if="false"
        color="blue-grey lighten-3"
        class="checkIcon"
        :class="checkIconAddon"
      >
        mdi-cloud-check-variant-outline
      </v-icon>
      <div v-if="checkIconVisible" class="blue-grey--text text--lighten-3">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="checkIcon"
          width="28"
          height="28"
          viewBox="0 0 24 24"
        >
          <g
            fill="none"
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.75"
          >
            <path
              stroke-dasharray="60"
              stroke-dashoffset="60"
              d="M3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12Z"
            >
              <animate
                fill="freeze"
                attributeName="stroke-dashoffset"
                dur="0.5s"
                values="60;0"
              />
            </path>
            <path stroke-dasharray="14" stroke-dashoffset="14" d="M8 12L11 15L16 10">
              <animate
                fill="freeze"
                attributeName="stroke-dashoffset"
                begin="0.6s"
                dur="0.2s"
                values="14;0"
              />
            </path>
          </g>
        </svg>
      </div>
    </div>

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
  name: 'ApiWrapperAppend',
  components: { ButtonRetry },
  props: {
    wrapper: {
      required: true,
      type: Object,
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
    checkIconVisible() {
      return (
        !(this.wrapper.hasServerError || this.wrapper.dirty) &&
        this.wrapper.status === 'success'
      )
    },
  },
}
</script>

<style scoped>
.checkIconContainer {
  position: absolute;
  right: 0;
  margin-top: -2px;
}

.v-input--dense .checkIconContainer {
  margin-top: 0;
}

.v-icon.checkIcon {
  transition: opacity 0.2s ease-out;
  opacity: 0;
}

div.v-input--checkbox .v-icon.checkIcon {
  top: 5px;
  right: 40px;
}

.v-icon.checkIcon.visible {
  opacity: 1;
}

.v-icon.checkIcon.hidden {
  transition: none;
}
</style>
