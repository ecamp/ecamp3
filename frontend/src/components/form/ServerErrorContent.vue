<template>
  <div v-if="!!serverError.name || !!serverError.response">
    <template
      v-if="
        serverError.name === 'ServerException' &&
          serverError.response &&
          serverError.response.status === 422
      ">
      <div class="title">
        Validation error
      </div>
      <ul>
        <li
          v-for="(violation, index) in serverError.response.data.violations"
          :key="index">
          <div>
            <b>{{ violation.propertyPath }}</b>: {{ violation.message }}
          </div>
        </li>
      </ul>
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
