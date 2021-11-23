<template>
  <v-container fluid>
    <content-card :title="$tc('views.camps.title', camps.items.length)" max-width="800" toolbar>
      <v-list class="py-0">
        <template v-if="camps._meta.loading">
          <v-skeleton-loader type="list-item-two-line" height="64" />
          <v-skeleton-loader type="list-item-two-line" height="64" />
        </template>
        <v-list-item
          v-for="camp in upcomingCamps"
          :key="camp.id"
          two-line
          :to="campRoute(camp)">
          <v-list-item-content>
            <v-list-item-title>{{ camp.title }}</v-list-item-title>
            <v-list-item-subtitle>
              {{ camp.name }} - {{ camp.motto }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-list-item>
          <v-list-item-content />
          <v-list-item-action>
            <button-add
              icon="mdi-plus"
              :to="{ name: 'camps/create' }">
              {{ $tc('views.camps.create') }}
            </button-add>
          </v-list-item-action>
        </v-list-item>
      </v-list>
      <v-expansion-panels
        v-if="prototypeCamps.length > 0 || pastCamps.length > 0"
        multiple
        flat
        accordion>
        <v-expansion-panel v-if="prototypeCamps.length > 0">
          <v-expansion-panel-header>
            <h3>
              {{ $tc('views.camps.prototypeCamps') }}
            </h3>
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-list class="py-0">
              <v-list-item
                v-for="camp in prototypeCamps"
                :key="camp.id"
                two-line
                :to="campRoute(camp)">
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
        <v-expansion-panel v-if="pastCamps.length > 0">
          <v-expansion-panel-header>
            <h3>
              {{ $tc('views.camps.pastCamps') }}
            </h3>
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-list class="py-0">
              <v-list-item
                v-for="camp in pastCamps"
                :key="camp.id"
                two-line
                :to="campRoute(camp)">
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
      </v-expansion-panels>
    </content-card>
  </v-container>
</template>

<script>
import { campRoute } from '@/router.js'
import ContentCard from '@/components/layout/ContentCard.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'

export default {
  name: 'Camps',
  components: {
    ContentCard,
    ButtonAdd
  },
  computed: {
    camps () {
      return this.api.get().camps()
    },
    prototypeCamps () {
      return this.camps.items.filter(c => c.isPrototype)
    },
    upcomingCamps () {
      return this.camps.items
        .filter(c => !c.isPrototype)
        .filter(c => c.periods().items.some(p => new Date(p.end) > new Date()))
    },
    pastCamps () {
      return this.camps.items
        .filter(c => !c.isPrototype)
        .filter(c => !c.periods().items.some(p => new Date(p.end) > new Date()))
    }
  },
  mounted () {
    this.api.reload(this.camps)
  },
  methods: {
    campRoute
  }
}
</script>

<style scoped>
  .v-expansion-panel-content >>> .v-expansion-panel-content__wrap {
    padding: 0 !important;
    }
</style>
