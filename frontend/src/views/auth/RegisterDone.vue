<template>
  <v-content>
    <v-toolbar absolute width="100%"
               color="blue-grey darken-4" dark>
      <v-toolbar-title>
        <v-btn icon class="title">🏕</v-btn>
        eCamp
      </v-toolbar-title>
    </v-toolbar>
    <v-container class="fill-height justify-center align-center" fluid>
      <v-col cols="12" sm="8" md="4">
        <v-card class="elevation-12">
          <v-toolbar dense color="blue-grey lighten-5">
            <v-toolbar-title>Registering</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-alert type="success">
              Successful registered.
            </v-alert>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn color="primary" :to="{name: 'login'}">
              Login
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-container>
  </v-content>
</template>

<script>
export default {
  name: 'RegisterDone'
}
</script>

<style>
  :root {
    --input-padding-x: .75rem;
    --input-padding-y: .4rem;
  }

  #app {
    width: 100%;
    height: 100%;
  }

  .form-register {
    width: 100%;
    max-width: 420px;
    padding: 15px;
    margin: 0 auto;
  }

  .form-label-group {
    position: relative;
    margin-bottom: 1rem;
  }

  .form-label-group > input,
  .form-label-group > label {
    padding: var(--input-padding-y) var(--input-padding-x);
  }

  .form-label-group > label {
    position: absolute;
    top: 0;
    left: 0;
    display: block;
    width: 100%;
    margin-bottom: 0; /* Override default `<label>` margin */
    line-height: 1.5;
    color: #495057;
    border: 1px solid transparent;
    border-radius: .25rem;
    transition: all .1s ease-in-out;
  }

  .form-label-group input::-webkit-input-placeholder {
    color: transparent;
  }

  .form-label-group input:-ms-input-placeholder {
    color: transparent;
  }

  .form-label-group input::-ms-input-placeholder {
    color: transparent;
  }

  .form-label-group input::-moz-placeholder {
    color: transparent;
  }

  .form-label-group input::placeholder {
    color: transparent;
  }

  .form-label-group input:not(:placeholder-shown) {
    padding-top: calc(var(--input-padding-y) + var(--input-padding-y) * (2 / 3));
    padding-bottom: calc(var(--input-padding-y) / 3);
  }

  .form-label-group input:not(:placeholder-shown) ~ label {
    padding-top: calc(var(--input-padding-y) / 4);
    padding-bottom: calc(var(--input-padding-y) / 4);
    font-size: 10px;
    color: #777;
  }
</style>
