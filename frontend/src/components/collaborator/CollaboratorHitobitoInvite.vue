<template>
  <v-btn
    class="my-n2 px-3 px-sm-4"
    color="blue-grey-darken-2"
    variant="text"
    min-width="0"
    data-testid="hitobito-invite-cta"
    :to="inviteRoute"
  >
    <v-icon start size="150%" :color="iconColor" :icon="icon" />
    {{ $t('components.collaborator.collaboratorHitobitoInvite.inviteCta', { provider }) }}
  </v-btn>
</template>

<script>
import { providerIcon, providerIconColor, providerNameKey } from '@/plugins/hitobito.js'
import { campRoute } from '@/router.js'

export default {
  name: 'CollaboratorHitobitoInvite',
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
    inviteRoute() {
      return campRoute(this.camp, 'hitobitoInvite')
    },
  },
}
</script>
