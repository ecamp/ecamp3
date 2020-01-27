<template>
  <v-card>
    <v-toolbar dense color="blue-grey lighten-5">
      <v-toolbar-title>Home</v-toolbar-title>
    </v-toolbar>
    <v-list class="pt-0">
      <v-list-item :to="{ name: 'login' }">
        <v-list-item-icon>
          <v-icon>mdi-login</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            Login
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'logout' }">
        <v-list-item-icon>
          <v-icon>mdi-logout</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            Logout
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'camps' }">
        <v-list-item-icon>
          <v-icon>mdi-format-list-bulleted-triangle</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            Camp List
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'camp', params: { campUri: '/camp/1fb176e0' }}">
        <v-list-item-icon>
          <v-icon>mdi-information</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            Camp basic info
          </v-list-item-title>
          <v-list-item-subtitle>
            [Hardcoded CampId, router demonstration only]
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'camp/periods', params: { campUri: '/camp/1fb176e0' }}">
        <v-list-item-icon>
          <v-icon>mdi-calendar-text</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            Camp periods
          </v-list-item-title>
          <v-list-item-subtitle>
            [Hardcoded CampId, router demonstration only]
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
      <v-list-item :to="{ name: 'camp/picasso', params: { campUri: '/camp/1fb176e0' }}">
        <v-list-item-icon>
          <v-icon>mdi-calendar-month</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            Camp picasso
          </v-list-item-title>
          <v-list-item-subtitle>
            [Hardcoded CampId, router demonstration only]
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<script>
export default {
  name: 'Home'
}
</script>

<style lang="scss" scoped>
  .v-application--is-ltr {
    .v-list-item__action:first-child, .v-list-item__icon:first-child {
      margin-right: 20px;
    }
  }
</style>
