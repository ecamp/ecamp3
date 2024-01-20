<template>
  <v-container fluid>
    <content-card :title="$tc('views.camps.title')" max-width="800" toolbar>
      <template #title-actions>
        <UserMeta v-if="!$vuetify.breakpoint.mdAndUp" avatar-only btn-classes="mr-n4" />
        <ButtonAdd v-else icon="mdi-plus" class="mr-n2" :to="{ name: 'camps/create' }">
          {{ $tc('views.camps.create') }}
        </ButtonAdd>
      </template>
      <v-list class="py-0">
        <template v-if="loading">
          <v-skeleton-loader type="list-item-two-line" height="64" />
          <v-skeleton-loader type="list-item-two-line" height="64" />
        </template>
        <v-list-item
          v-for="camp in upcomingCamps"
          v-else
          :key="camp._meta.self"
          two-line
          :to="campRoute(camp)"
        >
          <v-list-item-content class="basis-auto">
            <v-list-item-title>
              <strong>
                {{ $vuetify.breakpoint.lgAndUp ? camp.title : camp.name }}
              </strong>
            </v-list-item-title>
            <v-list-item-subtitle>
              {{ camp.motto }}
            </v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-content class="text-right basis-auto">
            <v-list-item-title>
              {{ camp.dateText }}
            </v-list-item-title>
            <v-list-item-subtitle v-if="camp.organizer">
              {{ camp.organizer }}
            </v-list-item-subtitle>
          </v-list-item-content>
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
              <v-list-item
                v-for="camp in prototypeCamps"
                :key="camp._meta.self"
                two-line
                :to="campRoute(camp)"
              >
                <v-list-item-content>
                  <v-list-item-title>{{ camp.title }}</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ camp.name }} - {{ camp.motto }}
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
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
              <v-list-item
                v-for="camp in pastCamps"
                :key="camp._meta.self"
                two-line
                :to="campRoute(camp)"
              >
                <v-list-item-content class="basis-auto">
                  <v-list-item-title>
                    <strong>
                      {{ $vuetify.breakpoint.lgAndUp ? camp.title : camp.name }}
                    </strong>
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    {{ camp.motto }}
                  </v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-content class="text-right basis-auto">
                  <v-list-item-title>
                    {{ camp.dateText }}
                  </v-list-item-title>
                  <v-list-item-subtitle v-if="camp.organizer">
                    {{ camp.organizer }}
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </content-card>
  </v-container>
</template>

<script>
import { campRoute } from '@/router.js'
import { isAdmin } from '@/plugins/auth'
import ContentCard from '@/components/layout/ContentCard.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import { mapGetters } from 'vuex'
import UserMeta from '@/components/navigation/UserMeta.vue'

export default {
  name: 'Camps',
  components: {
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
      return this.camps.items
        .filter((c) => c.isPrototype)
        .map((camp) => ({
          ...camp,
          dateText: this.getCombinedDate(camp.periods().items),
        }))
    },
    nonPrototypeCamps() {
      return this.camps.items
        .filter((c) => !c.isPrototype)
        .map((camp) => ({
          ...camp,
          dateText: this.getCombinedDate(camp.periods().items),
        }))
    },
    upcomingCamps() {
      return this.nonPrototypeCamps.filter((c) =>
        c.periods().items.some((p) => new Date(p.end) > new Date())
      )
    },
    pastCamps() {
      return this.nonPrototypeCamps.filter(
        (c) => !c.periods().items.some((p) => new Date(p.end) > new Date())
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
    getCombinedDate(periods) {
      if (!periods.length) return
      const formatMY = new Intl.DateTimeFormat(this.$i18n.locale, {
        year: 'numeric',
        month: 'short',
      })
      return [...periods]
        .sort((a, b) => new Date(a.start) - new Date(b.start))
        .map((period) => {
          const start = new Date(period.start)
          const end = new Date(period.end)
          return formatMY.formatRange(start, end)
        })
        .join(' | ')
    },
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
