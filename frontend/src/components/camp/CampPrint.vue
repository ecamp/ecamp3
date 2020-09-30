<template>
  <content-group title="Print">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else>
      <e-text-field
        label="Name"
        readonly
        :value="camp().name" />
      <v-btn color="primary" class="mt-5" @click="print">Print now</v-btn>
    </div>
  </content-group>
</template>

<script>
import ContentGroup from '@/components/layout/ContentGroup'

export default {
  name: 'CampPrint',
  components: { ContentGroup },
  props: {
    camp: {
      type: Function,
      required: true
    }
  },
  data () {
    return {}
  },
  methods: {
    async print () {
      const result = await this.api.post('/printer', {
        campId: this.camp().id
      })
      console.log(result)
    }
  }
}
</script>

<style scoped>
</style>
