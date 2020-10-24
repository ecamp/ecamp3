<template>
  <v-container fluid>
    <content-card :title="$tc('views.camps.title', camps.items.length)" max-width="800">
      <v-list class="py-0">
        <template v-if="camps._meta.loading">
          <v-skeleton-loader type="list-item-two-line" height="64" />
          <v-skeleton-loader type="list-item-two-line" height="64" />
        </template>
        <v-list-item
          v-for="camp in camps.items"
          :key="camp.id"
          two-line>
          <v-list-item-content @click.prevent="goToCamp(camp)">
            <v-list-item-title>{{ camp.title }}</v-list-item-title>
            <v-list-item-subtitle>
              {{ camp.name }} - {{ camp.campType().organization().name }}
            </v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-action>
            <dialog-entity-delete :entity="camp">
              <template v-slot:activator="{ on }">
                <button-delete @click.prevent="on.click" />
              </template>
              {{ $tc('components.dialog.dialogEntityDelete.warningText') }}
              <ul>
                <li>{{ camp.title }}</li>
              </ul>
            </dialog-entity-delete>
          </v-list-item-action>
        </v-list-item>
        <v-divider />
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
    </content-card>
  </v-container>
</template>

<script>
import { campRoute } from '@/router'
import ContentCard from '@/components/layout/ContentCard'
import ButtonAdd from '@/components/buttons/ButtonAdd'
import ButtonDelete from '@/components/buttons/ButtonDelete'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete'

export default {
  name: 'Camps',
  components: {
    ContentCard,
    ButtonAdd,
    ButtonDelete,
    DialogEntityDelete
  },
  computed: {
    camps () {
      return this.api.get().camps()
    }
  },
  methods: {
    campRoute,
    goToCamp (camp) {
      router.push(campRoute(camp))
    }
  }
}
</script>

<style scoped>

</style>
