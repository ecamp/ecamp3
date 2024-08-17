<template>
  <v-container fluid>
    <content-card :title="$tc('views.camps.title')" max-width="800" toolbar>
      <template #title-actions>
        <UserMeta v-if="!$vuetify.breakpoint.mdAndUp" avatar-only btn-classes="mr-n4" />
      </template>
      <v-list class="py-0">
        <template v-if="loading">
          <v-skeleton-loader type="list-item-two-line" height="64" />
          <v-skeleton-loader type="list-item-two-line" height="64" />
        </template>
        <template v-else>
          <CampListItem
            v-for="camp in upcomingCamps"
            :key="camp._meta.self"
            :camp="camp"
          />
        </template>
        <v-list-item>
          <v-list-item-content />
          <v-list-item-action>
            <button-add icon="mdi-plus" :to="{ name: 'camps/create' }">
              {{ $tc('views.camps.create') }}
            </button-add>
          </v-list-item-action>
        </v-list-item>
      </v-list>
      <v-expansion-panels
        v-if="
          !loading && ((isAdmin() && prototypeCamps.length > 0) || pastCamps.length > 0)
        "
        multiple
        flat
        accordion
      >
        <v-expansion-panel v-if="isAdmin() && prototypeCamps.length > 0">
          <v-expansion-panel-header>
            <h3>
              {{ $tc('views.camps.prototypeCamps') }}
            </h3>
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-list class="py-0">
              <CampListItem
                v-for="camp in prototypeCamps"
                :key="camp._meta.self"
                :camp="camp"
              />
            </v-list>
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel v-if="!loading && pastCamps.length > 0">
          <v-expansion-panel-header>
            <h3>
              {{ $tc('views.camps.pastCamps') }}
            </h3>
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-list class="py-0">
              <CampListItem
                v-for="camp in pastCamps"
                :key="camp._meta.self"
                :camp="camp"
              />
            </v-list>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </content-card>
  </v-container>
</template>

<script>
import dayjs from '@/common/helpers/dayjs.js'
import { campRoute } from '@/router.js'
import { isAdmin } from '@/plugins/auth'
import ContentCard from '@/components/layout/ContentCard.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import { mapGetters } from 'vuex'
import UserMeta from '@/components/navigation/UserMeta.vue'
import CampListItem from '@/components/camp/CampListItem.vue'

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
    }
  },
  computed: {
    camps() {
      return this.api.get().camps()
    },
    prototypeCamps() {
      return this.camps.items.filter((c) => c.isPrototype)
    },
    nonPrototypeCamps() {
      return this.camps.items.filter((c) => !c.isPrototype)
    },
    upcomingCamps() {
      return this.nonPrototypeCamps.filter((c) =>
        c.periods().items.some((p) => dayjs(p.end).endOf('day').isAfter(dayjs()))
      )
    },
    pastCamps() {
      return this.nonPrototypeCamps.filter(
        (c) => !c.periods().items.some((p) => dayjs(p.end).endOf('day').isAfter(dayjs()))
      )
    },
    ...mapGetters({
      user: 'getLoggedInUser',
    }),
  },
  async mounted() {
    this.loadCamps()
  },
  methods: {
    campRoute,
    isAdmin,
    async loadCamps() {
      // Only reload camps if they were loaded before, to avoid console error
      if (this.camps._meta.self !== null) {
        this.api.reload(this.camps)
      }

      await this.camps._meta.load

      await Promise.all(
        this.nonPrototypeCamps.map((camp) => {
          camp.periods()._meta.load
        })
      )

      this.loading = false
    },
  },
}
</script>

<style scoped>
.v-expansion-panel-content:deep(.v-expansion-panel-content__wrap) {
  padding: 0 !important;
}
</style>
