<template>
  <v-list class="mx-n2">
    <v-list-item
      v-for="participant in participants"
      :key="participant.email"
      :title="displayName(participant)"
      :subtitle="participant.email"
    >
      <template #prepend>
        <v-avatar :color="avatarColor">
          <v-icon :color="iconColor" :icon="icon" />
        </v-avatar>
      </template>
    </v-list-item>
  </v-list>
</template>

<script>
import { participantDisplayName } from '@/components/campHitobitoInvite/participants.js'

export default {
  name: 'HitobitoParticipantList',
  props: {
    participants: { type: Array, required: true },
    icon: { type: String, required: true },
    iconColor: { type: String, required: true },
    avatarColor: { type: String, required: true },
  },
  methods: {
    displayName: participantDisplayName,
  },
}
</script>
