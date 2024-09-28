<template>
  <div>
    <generic-error-message v-if="error" :error="error" />
    <activity-list-period
      v-for="period in periods"
      :key="period._meta.self"
      :period="period"
      :camp="camp"
      :index="index"
      :content-type-names="['LearningObjectives', 'LearningTopics', 'Checklist']"
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
  `config/ActivityList-${props.index}`,
  async () => {
    await Promise.all([
      $api.get().contentTypes().$loadItems(),
      props.camp.periods().$loadItems(),
      props.camp.activities().$loadItems(),
      props.camp.categories().$loadItems(),
      props.camp.checklists().$loadItems(),
      $api
        .get()
        .checklistItems({
          'checklist.camp': props.camp._meta.self,
        })
        .$loadItems(),
    ])

    return props.options.periods.map((periodUri) => {
      return $api.get(periodUri)
    })
  }
)
</script>
