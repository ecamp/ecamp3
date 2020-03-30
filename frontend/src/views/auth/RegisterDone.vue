<template>
  <v-container class="sso--container fill-height justify-center align-center" fluid>
    <v-col cols="12" sm="8"
           md="5" lg="4"
           class="pa-0 pa-sm-4"
           :class="{'fill-height': $vuetify.breakpoint.xsOnly}">
      <v-card class="pa-8"
                    :tile="$vuetify.breakpoint.xsOnly"
                    :class="{'fill-min-height': $vuetify.breakpoint.xsOnly}">
        <h1 class="display-1">Konto erstellen…</h1>
        <v-alert type="success" class="my-4 text--green text--darken-2">
          Successfully registered.
        </v-alert>
        <v-spacer />
        <v-btn color="primary" :to="{name: 'login'}"
               x-large
               class="my-4" block>
          Login
        </v-btn>
      </v-card>
    </v-col>
    <photo-credit>
      Photo by Markus Rohner / Lotos
    </photo-credit>
  </v-container>
</template>

<script>
import PhotoCredit from '@/components/layout/PhotoCredit'

export default {
  name: 'RegisterDone',
  components: { PhotoCredit }
}
</script>

<style>
</style>
