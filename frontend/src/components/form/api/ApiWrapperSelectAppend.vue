<template>
  <div class="ec-api-select-append d-contents">
    <v-icon v-if="!checkIconVisible">mdi-menu-down</v-icon>
    <div>
      <!-- Success icon after saving -->
      <div class="ec-api-select-append__icon">
        <svg
          v-if="checkIconVisible"
          xmlns="http://www.w3.org/2000/svg"
          class="checkIcon blue-grey--text text--lighten-3"
          style="margin-top: -3px; margin-right: -1px"
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
                @mousedown.stop
                @mouseup.stop
                @click.stop="wrapper.on.save"
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
                @mousedown.stop
                @mouseup.stop
                @click.stop="wrapper.on.reset"
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
                @mousedown.stop
                @mouseup.stop
                @click.stop="wrapper.on.save"
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
                @mousedown.stop
                @mouseup.stop
                @click.stop="wrapper.on.reset"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </template>
            <span>{{ $tc('global.button.cancel') }}</span>
          </v-tooltip>
        </template>

        <!-- Retry button if loading failed -->
        <button-retry
          v-if="wrapper.hasLoadingError"
          text
          @click.stop="wrapper.on.reload"
          @mousedown.stop
          @mouseup.stop
        />
      </div>
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
    checkIconVisible() {
      return (
        !(this.wrapper.hasServerError || this.wrapper.dirty) &&
        this.wrapper.status === 'success'
      )
    },
  },
}
</script>

<style lang="scss" scoped>
.ec-api-select-append__check {
  opacity: 0;
}

.ec-api-wrapper--success .ec-api-select-append__check {
  opacity: 1;
}

.ec-api-wrapper--success.ec-api-wrapper--type-select .mdi-menu-down {
  transition: opacity 0.2s ease;
  opacity: 0;
}
</style>
