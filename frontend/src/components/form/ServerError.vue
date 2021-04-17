<template>
  <v-alert v-if="serverError"
           text outlined
           color="red darken-2" icon="mdi-alert-circle">
    <template v-if="!!serverError.name || !!serverError.response ">
      <div v-if="serverError.name === 'ServerException' && serverError.response && serverError.response.status === 422">
        <div class="title">Validation error:</div>
        <div v-for="(validation_messages, name) in serverError.response.data.validation_messages" :key="name">
          <div>
            <b>{{ name }}</b>: <span v-for="(message, idx) in validation_messages" :key="idx">
              {{ message }}<span v-if="idx === validation_messages.length">, </span>
            </span>
          </div>
        </div>
      </div>
      <div v-else>
        {{ serverError.message }}
      </div>
    </template>
    <template v-else>
      {{ serverError }}
    </template>
  </v-alert>
</template>

<script>

export default {
  name: 'ServerError',
  props: {
    serverError: {
      type: [Object, String],
      default: null
    }
  }
}

</script>
