<template>
  <div class="form-register">
    <div class="text-center mb-4">
      <i class="zmdi zmdi-hc-5x zmdi-labels" />
    </div>
    <div class="text-center">
      Successful registered.
    </div>
    <hr style="margin-top: 40px">
    <div class="form-label-group">
      <router-link
        :to="{ name: 'login' }"
        class="btn btn-md btn-link btn-block">
        Login
      </router-link>
    </div>
  </div>
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
