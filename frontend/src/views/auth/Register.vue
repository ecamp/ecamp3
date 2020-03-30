<template>
  <auth-card>
    <h1 class="display-1 text-center">Konto erstellen</h1>
    <v-form @submit.prevent="register">
      <v-text-field
        v-model="username"
        label="Username"
        name="username"
        prefix="@"
        append-icon="mdi-at"
        filled
        hide-details="auto"
        class="my-2 my-sm-4"
        :dense="$vuetify.breakpoint.xsOnly"
        type="text" />

      <v-text-field
        v-model="fullname"
        label="Voller Name"
        name="fullname"
        append-icon="mdi-account-outline"
        filled
        hide-details="auto"
        class="my-2 my-sm-4"
        :dense="$vuetify.breakpoint.xsOnly"
        type="text" />

      <v-text-field
        v-model="email"
        label="eMail"
        name="email"
        hide-details="auto"
        filled
        append-icon="mdi-email-outline"
        class="my-2 my-sm-4"
        :dense="$vuetify.breakpoint.xsOnly"
        type="text" />

      <v-text-field
        v-model="pw1"
        label="Password"
        name="password"
        filled
        :rules="pw1Rules"
        validate-on-blur
        append-icon="mdi-lock-outline"
        hide-details="auto"
        class="my-2 my-sm-4"
        :dense="$vuetify.breakpoint.xsOnly"
        type="password" />

      <v-text-field
        v-model="pw2"
        label="Password erneut eingeben"
        name="password"
        filled
        :rules="pw2Rules"
        validate-on-blur
        hide-details="auto"
        class="my-2 my-sm-4"
        :dense="$vuetify.breakpoint.xsOnly"
        append-icon="mdi-lock-outline"
        type="password" />

      <v-checkbox
        v-model="tos"
        required
        hide-details="auto"
        class="my-2 my-sm-4 align-center">
        <template v-slot:label>
          <span :class="{'body-2':$vuetify.breakpoint.xsOnly}">
            Die Nutzungs&shy;bedingungen akzeptieren
          </span>
        </template>
        <template v-slot:append>
          <v-btn text min-width="0"
                 title="Öffnen"
                 target="_blank"
                 class="px-1" to="#">
            <v-icon small>mdi-open-in-new</v-icon>
          </v-btn>
        </template>
      </v-checkbox>
      <v-btn color="primary" :disabled="!formComplete"
             block x-large
             @click="register">
        Register
      </v-btn>
    </v-form>
    <p class="mt-8 mb-0 text--secondary text-center">
      Du hast bereits einen Account?<br>
      <router-link :to="{ name: 'login' }">Anmelden</router-link>
    </p>
  </auth-card>
</template>

<script>
import AuthCard from '@/components/layout/AuthCard'

export default {
  name: 'Register',
  components: {
    AuthCard
  },
  data () {
    return {
      username: '',
      fullname: '',
      email: '',
      pw1: '',
      pw2: '',
      tos: false
    }
  },
  computed: {
    formComplete () {
      return this.tos && (this.username !== '') && (this.email !== '') &&
        (this.pw1 !== '') && (this.pw2 !== '') &&
        (this.pw1 === this.pw2)
    },
    formData () {
      return {
        username: this.username,
        email: this.email,
        password: this.pw1
      }
    },
    pw2Rules () {
      return [
        v => (!!v && v) === this.pw1 || 'Nicht übereinstimmend'
      ]
    },
    pw1Rules () {
      return [
        v => v.length >= 8 || 'Mindestens 8 Zeichen lang sein'
      ]
    }
  },
  methods: {
    async register () {
      await this.$auth.register(this.formData)
      this.$router.push({ name: 'register-done' })
    }
  }
}
</script>

<style>

</style>
