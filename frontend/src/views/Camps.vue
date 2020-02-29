<template>
  <card-view title="Meine Camps" max-width="600">
    <v-list class="py-0">
      <template v-if="camps._meta.loading">
        <v-skeleton-loader type="list-item-two-line" height="64" />
        <v-skeleton-loader type="list-item-two-line" height="64" />
      </template>
      <v-list-item
        v-for="camp in camps.items"
        :key="camp.id"
        two-line
        :to="campRoute(camp)">
        <v-list-item-content>
          <v-list-item-title>{{ camp.title }}</v-list-item-title>
          <v-list-item-subtitle>
            {{ camp.name }} - {{ camp.camp_type().organization().name }}
          </v-list-item-subtitle>
        </v-list-item-content>
        <v-list-item-action>
          <v-btn
            icon
            @click.prevent="deleteCamp(camp, ...arguments)">
            <v-icon left>
              mdi-delete
            </v-icon>
          </v-btn>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </card-view>
</template>

<script>
import { campRoute } from '@/router'
const CardView = () => import('../components/CardView.vue')

export default {
  name: 'Camps',
  components: {
    CardView
  },
  computed: {
    camps () {
      return this.api.get().camps()
    }
  },
  methods: {
    deleteCamp (camp) {
      this.api.del(camp)
    },
    campRoute
  }
}
</script>

<style scoped>

</style>
