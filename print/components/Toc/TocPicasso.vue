<template>
  <li>
    <div class="toc-element-level-1">
      {{ $t('print.picasso.title') }}
    </div>
    <ul>
      <generic-error-message v-if="error" :error="error" />
      <li v-for="period in periods" v-else :key="period._meta.self">
        <div class="toc-element toc-element-level-2">
          <a :href="`#content_${index}_period_${period.id}`"
            >{{ $t('entity.period.name') }} {{ period.description }}</a
          >
        </div>
      </li>
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
  `TocPicasso-${props.index}`,
  async () => {
    await props.camp.periods().$loadItems()

    return props.options.periods.map((periodUri) => {
      return $api.get(periodUri)
    })
  }
)
</script>
