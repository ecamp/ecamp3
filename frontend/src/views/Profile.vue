<template>
  <card-view :title="'Profil: ' + profile.username" max-width="900">
    <v-col>
      <api-input
        label="Email"
        :uri="profile._meta.self"
        fieldname="mail"
        :value="profile.mail"
        :editing="false"
        required />
    </v-col>
  </card-view>
</template>

<script>
import ApiInput from '../components/form/ApiInput'

const CardView = () => import('../components/CardView')
export default {
  name: 'Home',
  components: { ApiInput, CardView },
  computed: {
    profile () {
      return this.api.get().profile()
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
