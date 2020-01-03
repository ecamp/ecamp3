<template>
  <v-card class="pa-3">
    <ul>
      <li>
        <router-link :to="{ name: 'home' }">
          Home
        </router-link>
      </li>
      <li>
        <router-link :to="{ name: 'login' }">
          Login
        </router-link>
      </li>
      <li>
        <router-link :to="{ name: 'logout' }">
          Logout
        </router-link>
      </li>
      <li>
        <router-link :to="{ name: 'camps', params: { groupName: 'Pfadi Bewegung Schweiz' } }">
          Camp List
        </router-link>
      </li>
      <li>
        <router-link :to="{ name: 'camp', params: { groupName: 'Pfadi Bewegung Schweiz', campUri: 'camp/1fb176e0' } }">
          [Hardcoded CampId, router demonstration only] Camp basic info
        </router-link>
      </li>
      <li>
        <router-link :to="{ name: 'camp/periods', params: { groupName: 'Pfadi Bewegung Schweiz', campUri: 'camp/1fb176e0' } }">
          [Hardcoded CampId, router demonstration only] Camp periods
        </router-link>
      </li>
    </ul>
  </v-card>
</template>

<script>
export default {
  name: 'Home'
}
</script>
