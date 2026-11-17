<template>
  <content-group
    :title="$t('components.campAdmin.campHitobitoSync.title')"
    icon="mdi-sync"
  >
    <v-list class="py-0" color="transparent" lines="two">
      <v-list-item class="px-0">
        <template #prepend>
          <v-icon style="opacity: 1" size="100%" :color="iconColor" :icon="icon" />
        </template>
        <v-list-item-title>
          {{ $t('components.campAdmin.campHitobitoSync.linked', { provider }) }}
        </v-list-item-title>
        <v-list-item-subtitle>
          <a
            :href="eventUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="text-primary"
            data-testid="hitobito-event-link"
          >
            {{ $t('components.campAdmin.campHitobitoSync.openInProvider', { provider }) }}
            <v-icon icon="mdi-open-in-new" size="x-small" />
          </a>
        </v-list-item-subtitle>
        <template #append>
          <ButtonEdit
            icon="mdi-sync"
            variant="tonal"
            data-testid="hitobito-sync-cta"
            :to="syncRoute"
          >
            {{ $t('components.campAdmin.campHitobitoSync.sync') }}
          </ButtonEdit>
        </template>
      </v-list-item>
    </v-list>
  </content-group>
</template>

<script>
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import {
  hitobitoEventWebUrl,
  providerIcon,
  providerIconColor,
  providerNameKey,
} from '@/plugins/hitobito.js'
import { campRoute } from '@/router.js'

export default {
  name: 'CampHitobitoSync',
  components: { ButtonEdit, ContentGroup },
  props: {
    camp: { type: Object, required: true },
  },
  computed: {
    provider() {
      return this.$t(providerNameKey(this.camp.hitobitoProvider))
    },
    icon() {
      return providerIcon(this.camp.hitobitoProvider)
    },
    iconColor() {
      return providerIconColor(this.camp.hitobitoProvider)
    },
    eventUrl() {
      return hitobitoEventWebUrl(this.camp.hitobitoProvider, this.camp.hitobitoEventId)
    },
    syncRoute() {
      return campRoute(this.camp, 'hitobitoSync')
    },
  },
}
</script>
