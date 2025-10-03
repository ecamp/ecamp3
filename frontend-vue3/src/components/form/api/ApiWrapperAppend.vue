<template>
  <div class="d-flex" style="margin-top: -5px">
    <!-- Success icon after saving -->
    <div class="checkIconContainer">
      <v-icon color="green" class="checkIcon" :class="checkIconAddon">
        mdi-content-save
      </v-icon>
    </div>

    <!-- Retry/Cancel button if saving failed -->
    <template v-if="wrapper.hasServerError">
      <v-tooltip class="ml-auto" location="bottom">
        <template #activator="{ props }">
          <v-btn
            fab
            dark
            size="x-small"
            variant="flat"
            color="error"
            type="submit"
            class="mr-1"
            :aria-label="$t('global.button.tryagain')"
            v-bind="props"
            @click="wrapper.on.save"
          >
            <v-icon>mdi-refresh</v-icon>
          </v-btn>
        </template>
        <span>{{ $t('global.button.tryagain') }}</span>
      </v-tooltip>
      <v-tooltip class="ml-auto" location="bottom">
        <template #activator="{ props }">
          <v-btn
            fab
            dark
            size="x-small"
            variant="flat"
            color="grey"
            :aria-label="$t('global.button.cancel')"
            v-bind="props"
            @click="wrapper.on.reset"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </template>
        <span>{{ $t('global.button.cancel') }}</span>
      </v-tooltip>
    </template>

    <template v-else-if="!wrapper.autoSave">
      <v-tooltip v-if="wrapper.dirty" class="ml-auto" location="bottom">
        <template #activator="{ props }">
          <v-btn
            fab
            dark
            size="x-small"
            variant="flat"
            color="success"
            type="submit"
            class="mr-1"
            :aria-label="$t('global.button.save')"
            v-bind="props"
          >
            <v-icon>mdi-check</v-icon>
          </v-btn>
        </template>
        <span>{{ $t('global.button.save') }}</span>
      </v-tooltip>
      <v-tooltip class="ml-auto" location="bottom">
        <template #activator="{ props }">
          <v-btn
            fab
            dark
            size="x-small"
            variant="flat"
            color="grey"
            :aria-label="$t('global.button.cancel')"
            v-bind="props"
            @click="wrapper.on.reset"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </template>
        <span>{{ $t('global.button.cancel') }}</span>
      </v-tooltip>
    </template>

    <!-- Retry button if loading failed -->
    <button-retry v-if="wrapper.hasLoadingError" text @click="wrapper.on.reload" />
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

<style scoped>
.checkIconContainer {
  position: absolute;
}
.v-icon.checkIcon {
  position: relative;
  top: -11px;
  right: 13px;
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
