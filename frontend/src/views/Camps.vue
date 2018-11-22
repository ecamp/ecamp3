<template>
  <section class="container">
    <h3>Camps</h3>
    <ul>
      <li
        v-for="campId in campIdList"
        :key="campId">
        <router-link
          :to="{ name: 'camp', params: { campId: campId } }">{{ campId }}</router-link>
      </li>
    </ul>
  </section>
</template>

<script>
export default {
  name: 'Camps',
  data () {
    return {
      campIdList: []
    }
  },
  computed: {
    apiUrl () {
      return process.env.VUE_APP_ROOT_API + '/camp'
    }
  },
  created () {
    this.fetchFromAPI()
  },
  methods: {
    async fetchFromAPI () {
      try {
        this.campIdList = (await this.axios.get(this.apiUrl)).data._embedded.items.map(item => item.id)
      } catch (error) {
        this.messages = [{
          type: 'danger',
          text: 'Could not get camp list. ' + error
        }]
      }
    }
  }
}
</script>

<style scoped>

</style>
