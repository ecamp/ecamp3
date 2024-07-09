<template>
  <div>
    <generic-error-message v-if="error" :error="error" />
    <picasso-period
      v-for="period in periods"
      v-else
      :key="period._meta.self"
      :period="period"
      :camp="camp"
      :orientation="options.orientation"
      :landscape="landscape"
      :index="index"
    />
  </div>
</template>

<script setup>
const props = defineProps({
  options: { type: Object, required: false, default: null },
  camp: { type: Object, required: true },
  config: { type: Object, required: true },
  index: { type: Number, required: true },
})

const { $api } = useNuxtApp()

const { data: periods, error } = await useAsyncData(
  `config/Picasso- ${props.index}`,
  async () => {
    await Promise.all([
      props.camp.periods().$loadItems(),
      props.camp.activities().$loadItems(),
      props.camp.categories().$loadItems(),
      props.camp.campCollaborations().$loadItems(),
      props.camp.profiles().$loadItems(),
    ])

    return props.options.periods.map((periodUri) => {
      return $api.get(periodUri)
    })
  }
)
</script>

<script>
export default {
  computed: {
    landscape() {
      return this.options.orientation === 'L'
    },
  },
}
</script>
