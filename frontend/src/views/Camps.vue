<template>
  <v-container fluid>
    <content-card :title="$tc('views.camps.myCamps', camps.items.length)" max-width="800">
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
              {{ camp.name }} - {{ camp.campType().organization().name }}
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
        <v-divider />
        <v-list-item>
          <v-list-item-content />
          <v-list-item-action>
            <button-add
              icon="mdi-plus"
              :to="{ name: 'camps/create' }">
              {{ $t('views.camps.create') }}
            </button-add>
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </content-card>
  </v-container>
</template>

<script>
import { campRoute } from '@/router'
import ContentCard from '@/components/layout/ContentCard'
import ButtonAdd from '@/components/buttons/ButtonAdd'

export default {
  name: 'Camps',
  components: {
    ContentCard,
    ButtonAdd
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
