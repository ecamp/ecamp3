<template>
  <section class="container">
    <h3>Camps</h3>

    <b-spinner
      v-if="loading"
      variant="primary"
      label="Loading" />

    <ul v-if="!loading">
      <li
        v-for="camp in camps"
        :key="camp.id">
        <router-link
          :to="{ name: 'camp', params: { campId: camp.id } }">
          {{ camp.name }} / {{ camp.id }}
        </router-link>
      </li>
    </ul>

    <span v-if="!loading && camps.length===0">
      Keine Lager gefunden.
    </span>
  </section>
</template>

<script>

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Camps',
  data () {
    return {
    }
  },
  // make states available
  computed: mapState({
    camps: state => state.camps.camps,
    loading: state => state.shared.loading
  }),
  created () {
    this.fetchAll()
  },
  methods: {
    ...mapActions('camps', [
      'fetchAll'
    ])
  }
}
</script>

<style scoped>

</style>
