<template>
  <div class="tw-break-after-page">
    <generic-error-message v-if="error" :error="error" />
    <program-period
      v-for="period in periods"
      v-else
      :key="period._meta.self"
      :period="period"
      :camp="camp"
      :show-daily-summary="options.dayOverview || false"
      :show-activities="true"
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
  `config/Program-${props.index}`,
  async () => {
    await Promise.all([
      $api.get().contentTypes().$loadItems(),
      props.camp.periods().$loadItems(),
      props.camp.activities().$loadItems(),
      props.camp.categories().$loadItems(),
      props.camp.materialLists().$loadItems(),
      props.camp.campCollaborations().$loadItems(),
    ])

    return props.options.periods.map((periodUri) => {
      return $api.get(periodUri)
    })
  }
)
</script>
