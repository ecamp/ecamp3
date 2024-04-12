<template>
  <v-container fluid>
    <content-card
      :title="$tc('views.invitations.personalInvitations')"
      max-width="800"
      toolbar
    >
      <template #title-actions>
        <UserMeta v-if="!$vuetify.breakpoint.mdAndUp" avatar-only btn-classes="mr-n4" />
      </template>
      <v-list class="py-0">
        <template v-if="loading">
          <v-skeleton-loader type="list-item-two-line" height="64" />
          <v-skeleton-loader type="list-item-two-line" height="64" />
        </template>
        <PersonalInvitations v-else />
      </v-list>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import { mapGetters } from 'vuex'
import UserMeta from '@/components/navigation/UserMeta.vue'
import PersonalInvitations from '../components/personal_invitations/PersonalInvitations.vue'

export default {
  name: 'Invitations',
  components: {
    PersonalInvitations,
    UserMeta,
    ContentCard,
  },
  computed: {
    loading() {
      return this.api.get().personalInvitations().loading
    },
    ...mapGetters({
      user: 'getLoggedInUser',
    }),
  },
}
</script>

<style scoped>
.v-expansion-panel-content:deep(.v-expansion-panel-content__wrap) {
  padding: 0 !important;
}
</style>
