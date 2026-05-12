<template>
  <v-container fluid>
    <content-card :title="$t('views.camps.title')" max-width="800" toolbar>
      <template #title-actions>
        <UserMeta v-if="!$vuetify.display.mdAndUp" avatar-only />
      </template>
      <v-list class="py-0">
        <template v-if="loading">
          <v-skeleton-loader type="list-item-two-line" height="64" class="pa-4" />
          <v-skeleton-loader type="list-item-two-line" height="64" class="pa-4" />
        </template>
        <template v-else>
          <CampListItem
            v-for="{ camp, periods: upcomingPeriods } in upcomingCamps"
            :key="camp._meta.self"
            :camp="camp"
            :periods="upcomingPeriods"
          />
        </template>
        <v-list-item lines="two">
          <template #append>
            <v-list-item-action>
              <button-add
                data-testid="create-camp-button"
                icon="mdi-plus"
                :to="{ name: 'camps/create' }"
              >
                {{ $t('views.camps.create') }}
              </button-add>
            </v-list-item-action>
          </template>
        </v-list-item>
      </v-list>
      <v-expansion-panels
        v-if="
          !loading && ((isAdmin && prototypeCamps.length > 0) || pastCamps.length > 0)
        "
        v-model="openedPanels"
        multiple
        flat
        variant="accordion"
      >
        <v-expansion-panel v-if="isAdmin && prototypeCamps.length > 0">
          <v-expansion-panel-title>
            <h3>
              {{ $t('views.camps.prototypeCamps') }}
            </h3>
          </v-expansion-panel-title>
          <v-expansion-panel-text>
            <v-list class="py-0">
              <CampListItem
                v-for="camp in prototypeCamps"
                :key="camp._meta.self"
                :camp="camp"
              />
            </v-list>
          </v-expansion-panel-text>
        </v-expansion-panel>
        <v-expansion-panel v-if="!loading && pastCamps.length > 0">
          <v-expansion-panel-title>
            <h3>
              {{ $t('views.camps.pastCamps') }}
            </h3>
          </v-expansion-panel-title>
          <v-expansion-panel-text>
            <v-list class="py-0">
              <CampListItem
                v-for="{ camp, periods: pastperiods } in pastCamps"
                :key="camp._meta.self"
                :camp="camp"
                :periods="pastperiods"
              />
            </v-list>
          </v-expansion-panel-text>
        </v-expansion-panel>
      </v-expansion-panels>
    </content-card>
  </v-container>
</template>

<script>
import dayjs from '@/common/helpers/dayjs.js'
import { isAdmin } from '@/plugins/auth'
import ContentCard from '@/components/layout/ContentCard.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import UserMeta from '@/components/navigation/UserMeta.vue'
import CampListItem from '@/components/camp/CampListItem.vue'
import { groupBy } from 'lodash-es'

export default {
  name: 'Camps',
  components: {
    CampListItem,
    UserMeta,
    ContentCard,
    ButtonAdd,
  },
  data: function () {
    return {
      loading: true,
      isAdmin: false,
      openedPanels: [],
    }
  },
  head() {
    return {
      title: this.$t('views.camps.title'),
    }
  },
  computed: {
    currentUserLink() {
      return this.$store.getters.getLoggedInUser?._meta.self
    },
    camps() {
      return this.api.get().camps({ campCollaborator: this.currentUserLink })
    },
    periods() {
      return this.api.get().periods({ campCollaborator: this.currentUserLink })
    },
    prototypeCamps() {
      return this.api.get().camps({ isPrototype: true }).items
    },
    upcomingCamps() {
      return Object.values(
        groupBy(
          this.periods.items.filter((p) => dayjs(p.end).endOf('day').isAfter(dayjs())),
          (p) => p.camp()._meta.self
        )
      ).map((periods) => ({
        camp: periods[0].camp(),
        periods: periods,
      }))
    },
    pastCamps() {
      return Object.values(
        groupBy(
          this.periods.items
            .filter((p) => !dayjs(p.end).endOf('day').isAfter(dayjs()))
            .sort((p1, p2) => dayjs(p2.start).unix() - dayjs(p1.start).unix()),
          (p) => p.camp()._meta.self
        )
      )
        .map((periods) => ({
          camp: periods[0].camp(),
          periods: periods,
        }))
        .filter(({ camp }) => !camp.isPrototype)
    },
  },
  async mounted() {
    this.loadCamps()
    this.isAdmin = isAdmin()
  },
  methods: {
    async loadCamps() {
      await this.$auth.loadUser()
      // Only reload camps if they were loaded before, to avoid console error
      if (this.camps._meta.self !== null) {
        this.api.reload(this.camps)
      }

      await Promise.all([this.camps._meta.load, this.periods._meta.load])

      if (this.upcomingCamps.length === 0 && this.pastCamps.length > 0) {
        let pastCampsIndex = 0
        if (this.isAdmin && this.prototypeCamps.length > 0) {
          pastCampsIndex = 1
        }
        this.openedPanels = [pastCampsIndex]
      }
      this.loading = false
    },
  },
}
</script>

<style scoped>
:deep(.v-expansion-panel-text .v-expansion-panel-text__wrapper) {
  padding: 0 !important;
}
</style>
