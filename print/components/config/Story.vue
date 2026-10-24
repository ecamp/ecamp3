<template>
  <div>
    <generic-error-message v-if="error" :error="error" />
    <summary-period
      v-for="period in periods"
      :key="period._meta.self"
      :period="period"
      :filter="options.filter"
      :camp="camp"
      :index="index"
      :content-type="options.contentType"
      :type="'Story'"
      :page-size="pageSize"
    />
  </div>
</template>

<script setup>
const props = defineProps({
  options: { type: Object, required: false, default: null },
  camp: { type: Object, required: true },
  config: { type: Object, required: true },
  index: { type: Number, required: true },
  pageSize: { type: String, default: 'a4' },
})

const { $api } = useNuxtApp()

const { data: periods, error } = await useAsyncData(
  `config/Story-${props.index}`,
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
