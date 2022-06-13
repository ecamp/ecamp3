<template>
  <div class="d-flex" style="margin-top: -5px">
    <!-- Success icon after saving -->
    <div class="checkIconContainer">
      <v-icon color="green" class="checkIcon" :class="checkIconAddon">
        mdi-content-save
      </v-icon>
      <!--
      <v-btn
        fab
        dark
        depressed
        x-small
        color="success"
        class="checkIcon"
        :class="checkIconAddon">
        <v-icon>mdi-content-save</v-icon>
      </v-btn>
      -->
    </div>

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
            class="mr-1"
            v-on="on"
            @click="wrapper.on.save">
            <v-icon>mdi-refresh</v-icon>
          </v-btn>
        </template>
        <span>{{ $tc('global.button.tryagain') }}</span>
      </v-tooltip>
      <v-tooltip bottom class="ml-auto">
        <template #activator="{ on }">
          <v-btn
            fab
            dark
            depressed
            x-small
            color="grey"
            v-on="on"
            @click="wrapper.on.reset">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </template>
        <span>{{ $tc('global.button.cancel') }}</span>
      </v-tooltip>
    </template>

    <template v-else-if="!wrapper.autoSave && wrapper.dirty">
      <v-tooltip bottom class="ml-auto">
        <template #activator="{ on }">
          <v-btn
            fab
            dark
            depressed
            x-small
            color="success"
            type="submit"
            class="mr-1"
            v-on="on"
            @click="wrapper.on.save">
            <v-icon>mdi-check</v-icon>
          </v-btn>
        </template>
        <span>{{ $tc('global.button.save') }}</span>
      </v-tooltip>
      <v-tooltip bottom class="ml-auto">
        <template #activator="{ on }">
          <v-btn
            fab
            dark
            depressed
            x-small
            color="grey"
            v-on="on"
            @click="wrapper.on.reset">
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
      type="submit"
      @click="wrapper.on.reload" />
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
      type: Object
    }
  },
  computed: {
    checkIconAddon () {
      if (this.wrapper.hasServerError || this.wrapper.dirty) {
        return 'hidden'
      } else if (this.wrapper.status === 'success') {
        return 'visible'
      } else {
        return ''
      }
    }
  }
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
