<template>
  <div class="ma-1">
    <h3>Camps</h3>
    <ul>
      <li
        v-for="camp in camps"
        :key="camp.id">
        <router-link
          :to="{ name: 'camp', params: { campUri: camp._meta.self } }">
          {{ camp.name }} "{{ camp.title }}" - {{ camp.camp_type().organization().name }}
        </router-link>
        <v-btn
          color="warning"
          @click.prevent="deleteCamp(camp, ...arguments)">
          <v-icon left>
            mdi-delete
          </v-icon> Löschen
        </v-btn>
      </li>
    </ul>
    <v-btn
      color="primary"
      class="mt-2"
      @click="changeCampTitle">
      Change camp #1 title
    </v-btn>
  </div>
</template>

<script>
export default {
  name: 'Camps',
  computed: {
    camps () {
      return this.api.get('/camp').items
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
