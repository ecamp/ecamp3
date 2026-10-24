<template>
  <v-dialog
    :model-value="show"
    persistent
    max-width="480"
    data-testid="new-version-dialog"
    @update:model-value="onModelValue"
  >
    <v-card>
      <v-card-title class="text-wrap">
        {{ $t('global.newVersion.title') }}
      </v-card-title>
      <v-card-text>
        {{ $t('global.newVersion.description') }}
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn
          variant="text"
          data-testid="new-version-continue"
          @click="continueWithoutUpdating"
        >
          {{ $t('global.newVersion.updateLater') }}
        </v-btn>
        <v-btn
          color="primary"
          variant="elevated"
          data-testid="new-version-update"
          @click="update"
        >
          {{ $t('global.newVersion.update') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import {
  dismissNewVersion,
  updateToNewVersion,
  useNewVersionAvailable,
} from '@/helpers/newVersionAvailable.js'

export default {
  name: 'NewVersionAvailableDialog',
  setup() {
    return { newVersionAvailable: useNewVersionAvailable() }
  },
  computed: {
    show() {
      return this.newVersionAvailable
    },
  },
  methods: {
    continueWithoutUpdating() {
      dismissNewVersion()
    },
    update() {
      updateToNewVersion()
    },
    onModelValue(value) {
      if (!value) {
        dismissNewVersion()
      }
    },
  },
}
</script>
