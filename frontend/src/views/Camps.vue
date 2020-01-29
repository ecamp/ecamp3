<template>
  <v-card>
    <v-toolbar dense color="blue-grey lighten-5">
      <v-toolbar-title>Camps</v-toolbar-title>
    </v-toolbar>
    <v-list class="py-0">
      <v-skeleton-loader v-if="camps.loaded" type="list-item-two-line" />
      <v-list-item
        v-for="camp in camps.items"
        :key="camp.id"
        two-line
        :to="{ name: 'camp', params: { campUri: camp._meta.self } }">
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
  </v-card>
</template>

<script>
export default {
  name: 'Camps',
  computed: {
    camps () {
      return this.api.get().camps()
    }
  },
  methods: {
    changeCampTitle () {
      if (this.camps.length < 1) return
      const changedCamp = { ...this.camps[0] }
      changedCamp.title = changedCamp.title + ' HELLO'
      this.$store.commit('add', { [changedCamp._meta.self]: changedCamp })
    },
    deleteCamp (camp) {
      this.api.del(camp)
    }
  }
}
</script>

<style scoped>

</style>
