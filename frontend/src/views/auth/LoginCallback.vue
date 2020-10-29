<template>
  <v-sheet color="green dark-3">
    <v-container class="fill-height" fluid>
      <v-row align="center" justify="center">
        <v-card>
          <v-card-text><h3>Logging you in</h3></v-card-text>
        </v-card>
      </v-row>
    </v-container>
  </v-sheet>
</template>
<script>
import { refreshLoginStatus } from '@/plugins/auth'
import { get } from '@/plugins/store/apiPlugin'

export default {
  name: 'LoginCallback',
  beforeRouteEnter (to, from, next) {
    const redirect = to.query.redirect
    if (refreshLoginStatus()) {
      if (redirect) {
        // Redirect is set, go to this page
        next(decodeURI(redirect))
      } else {
        // If lastCampId is set, goto camp
        get().profile()._meta.load.then(p => {
          if (p.lastCampId) {
            next({ name: 'camp/program', params: { campId: p.lastCampId } })
          } else {
            next('/')
          }
        })
      }
    } else {
      next({ name: 'login', query: { redirect } })
    }
  }
}
</script>
<style scoped>
</style>
