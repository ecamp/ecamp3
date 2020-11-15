<!--
Admin screen of a camp: Displays MaterialLists and MaterialItems
-->

<template>
  <content-card>
    <v-toolbar>
      <v-card-title>{{ $tc('views.camp.material.title') }}</v-card-title>
      <v-spacer />
      <dialog-material-list-create :camp="camp()">
        <template v-slot:activator="{ on }">
          <button-add color="secondary"
                      class="mr-4"
                      text
                      v-on="on">
            Add Materiallist
          </button-add>
        </template>
      </dialog-material-list-create>
      <e-switch v-model="showActivityMaterial" label="Zeige Blockmaterial" />
    </v-toolbar>
    <v-card-text>
      <v-expansion-panels multiple>
        <material-lists v-for="period in camp().periods().items"
                        :key="period._meta.self"
                        :period="period"
                        :show-activity-material="showActivityMaterial" />
      </v-expansion-panels>
    </v-card-text>
  </content-card>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd'
import ContentCard from '@/components/layout/ContentCard'
import DialogMaterialListCreate from '@/components/dialog/DialogMaterialListCreate'
import MaterialLists from '@/components/camp/MaterialLists'

export default {
  name: 'Material',
  components: {
    ButtonAdd,
    ContentCard,
    DialogMaterialListCreate,
    MaterialLists
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      showActivityMaterial: false
    }
  }
}
</script>

<style scoped>
</style>
