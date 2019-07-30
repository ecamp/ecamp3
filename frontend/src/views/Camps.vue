<template>
  <div>
    <h3>Camps</h3>
    <ul>
      <li
        v-for="camp in camps"
        :key="camp().id">
        <router-link
          :to="{ name: 'camp', params: { campId: camp().id } }">
          {{ camp().name }} - {{ camp().camp_type().organization().name }}
        </router-link>
      </li>
    </ul>
    <button class="btn btn-primary" @click="changeCampName">Change camp #1 name in Vuex store</button>
  </div>
</template>

<script>
export default {
  name: 'Camps',
  computed: {
    apiUrl () {
      return process.env.VUE_APP_ROOT_API + '/camp'
    },
    camps () {
      return this.api('/camp').items
    }
  },
  methods: {
    changeCampName () {
      const changedCamp = { ...this.camps[0]() }
      changedCamp.name = changedCamp.name + ' HELLO'
      this.$store.commit('add', changedCamp)
    }
  }
}
</script>

<style scoped>

</style>
