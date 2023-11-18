<template>
  <div>
    <div class="error">
      <h1 v-if="error.statusCode === 404">
        {{ pageNotFound }}
      </h1>
      <div v-else>
        <h1>{{ otherError }}</h1>
        {{ error.message }}
      </div>
    </div>
  </div>
</template>

<script>
export default defineNuxtComponent({
  props: {
    error: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      pageNotFound: '404 Not Found',
      otherError: 'An error occurred',
    }
  },
  head() {
    const title = this.error.statusCode === 404 ? this.pageNotFound : this.otherError
    return {
      title,
    }
  },
})
</script>

<style scoped>
h1 {
  font-size: 20px;
}

.error {
  background-color: lightcoral;
}
</style>
