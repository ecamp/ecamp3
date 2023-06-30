<template>
  <div class="ec-api-checkbox-append mt-n1">
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
    </template>
    <template v-else-if="!wrapper.autoSave">
      <v-tooltip v-if="wrapper.dirty" bottom class="ml-auto">
        <template #activator="{ on }">
          <div class="rounded-full" tabindex="0" v-on="on">
            <v-btn
              fab
              dark
              depressed
              x-small
              color="success"
              type="submit"
              :aria-label="$tc('global.button.save')"
              @click="wrapper.on.save"
            >
              <v-icon>mdi-check</v-icon>
              <span>{{ $tc('global.button.save') }}</span>
            </v-btn>
          </div>
        </template>
      </v-tooltip>
    </template>

    <!-- Retry button if loading failed -->
    <button-retry v-if="wrapper.hasLoadingError" text @click.stop="$emit('reload')" />
  </div>
</template>

<script>
import ButtonRetry from '@/components/buttons/ButtonRetry.vue'

export default {
  name: 'ApiWrapperCheckboxAppend',
  components: { ButtonRetry },
  props: {
    wrapper: {
      required: true,
      type: Object,
    },
  },
}
</script>
