<template>
  <section class="container">
    <h3>Camps</h3>

    <div
      v-if="loading"
      class="spinner-border"
      role="status">
      Loading... (Boostrap spinner, coming soon with vue-boostrap/2.0.0-rc.12)
      <span class="sr-only">
        Loading...
      </span>
    </div>

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
