<template>
  <v-card-text class="text-body-1 pb-0">
    <h3>{{ title }}</h3>
  </v-card-text>
  <v-list v-bind="$attrs" class="my-0">
    <v-list-item
      v-for="participant in participants"
      :key="participant.email"
      :title="displayName(participant)"
      :subtitle="participant.email"
    >
      <template #prepend>
        <v-avatar :color="type === 'existing' ? 'grey-lighten-3' : 'green-lighten-4'">
          <v-icon
            :color="type === 'existing' ? 'grey-darken-1' : 'green-darken-3'"
            :icon="type === 'existing' ? 'mdi-account-check' : 'mdi-email-outline'"
          />
        </v-avatar>
      </template>
    </v-list-item>
  </v-list>
</template>

<script>
import { participantDisplayName } from '@/components/campHitobitoInvite/participants.js'

export default {
  name: 'HitobitoParticipantList',
  inheritAttrs: false,
  props: {
    title: { type: String, required: true },
    participants: { type: Array, required: true },
    type: {
      type: String,
      required: true,
      validator: (value) => ['invites', 'existing'].includes(value),
    },
  },
  methods: {
    displayName: participantDisplayName,
  },
}
</script>
