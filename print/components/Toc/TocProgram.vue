<template>
  <li>
    <div class="toc-element-level-1">
      {{ $t('print.program.title') }}
    </div>
    <ul>
      <generic-error-message v-if="error" :error="error" />
      <toc-program-period
        v-for="period in periods"
        v-else
        :key="period._meta.self"
        :period="period"
        :index="index"
      />
    </ul>
  </li>
</template>

<script setup>
const props = defineProps({
  options: { type: Object, required: false, default: null },
  camp: { type: Object, required: true },
  index: { type: Number, required: true },
})

const { $api } = useNuxtApp()

const { data: periods, error } = await useAsyncData(
  `TocProgram-${props.index}`,
  async () => {
    await Promise.all([
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
