<template>
  <div>
    <generic-error-message v-if="error" :error="error" />
    <summary-period
      v-for="period in periods"
      :key="period._meta.self"
      :period="period"
      :camp="camp"
      :index="index"
      :content-type="options.contentType"
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
  `config/SafetyConsiderations-${props.index}`,
  async () => {
    await Promise.all([
      $api.get().contentTypes().$loadItems(),
      props.camp.periods().$loadItems(),
      props.camp.activities().$loadItems(),
      props.camp.categories().$loadItems(),
    ])

    return props.options.periods.map((periodUri) => {
      return $api.get(periodUri)
    })
  }
)
</script>
