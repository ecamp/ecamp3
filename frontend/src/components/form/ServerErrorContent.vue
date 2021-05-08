<template>
  <div v-if="!!serverError.name || !!serverError.response ">
    <template v-if="serverError.name === 'ServerException' && serverError.response && serverError.response.status === 422">
      <div class="title">
        Validation error:
      </div>
      <div v-for="(validation_messages, name) in serverError.response.data.validation_messages" :key="name">
        <div>
          <b>{{ name }}</b>: <span v-for="(message, idx) in validation_messages" :key="idx">
            {{ message }}<span v-if="idx === validation_messages.length">, </span>
          </span>
        </div>
      </div>
    </template>
    <template v-else>
      {{ serverError.message }}
    </template>
  </div>
  <div v-else>
    {{ serverError }}
  </div>
</template>

<script>

export default {
  name: 'ServerErrorContent',
  props: {
    serverError: {
      type: [Object, String, Error],
      default: null
    }
  }
}

</script>
