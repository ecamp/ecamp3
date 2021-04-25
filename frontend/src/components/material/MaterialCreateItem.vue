<template>
  <ValidationObserver v-if="materialLists.length > 0"
                      ref="validation"
                      tag="tr"
                      class="newItemRow"
                      @submit.prevent="createMaterialItem">
    <td>
      <e-text-field
        ref="quantity"
        v-model="materialItem.quantity"
        dense
        :name="$tc('entity.materialItem.fields.quantity')"
        fieldname="quantity" />
    </td>
    <td>
      <e-text-field
        v-model="materialItem.unit"
        dense
        :name="$tc('entity.materialItem.fields.unit')"
        fieldname="unit" />
    </td>
    <td>
      <e-text-field
        v-model="materialItem.article"
        dense
        vee-rules="required"
        :name="$tc('entity.materialItem.fields.article')"
        fieldname="article" />
    </td>
    <td>
      <e-select
        v-model="materialItem.materialListId"
        dense
        vee-rules="required"
        :name="$tc('entity.materialList.name')"
        fieldname="materialListId"
        :items="materialLists" />
    </td>
    <td>
      <button-add hide-label @click="submitForm" />
    </td>
  </ValidationObserver>

  <tr v-else>
    <td colspan="5">
      <div>
        <p>
          {{ $tc('components.camp.materialCreateItem.noMaterialListAvailable') }}
        </p>
        <v-btn :to="campRoute(camp, 'admin')">
          <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-cogs</v-icon>
          {{ $tc('views.camp.navigationCamp.admin') }}
        </v-btn>
      </div>
    </td>
  </tr>
</template>

<script>
import { campRoute } from '@/router.js'
import { ValidationObserver } from 'vee-validate'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'

export default {
  name: 'MaterialCreateItem',
  components: { ValidationObserver, ButtonAdd },
  props: {
    camp: { type: Object, required: true },

    // API collection on which to post the new item
    materialItemCollection: { type: Object, required: true }
  },
  data () {
    return {
      materialItem: {}
    }
  },
  computed: {
    materialLists () {
      return this.camp.materialLists().items.map(l => ({
        value: l.id,
        text: l.name
      }))
    }
  },
  methods: {
    async submitForm () {
      const isValid = await this.$refs.validation.validate()
      if (isValid) {
        this.createMaterialItem()
      }
    },
    createMaterialItem () {
      const key = Date.now()
      const data = this.materialItem

      this.materialItem = {}
      this.$refs.quantity.focus()
      this.$refs.validation.reset()

      // post new item to the API collection
      const res = this.materialItemCollection.$post(data)

      // fire event to allow for eager adding before post has finished
      this.$emit('item-adding', key, data, res)
    },
    campRoute
  }
}
</script>

<style scoped>
  .newItemRow {
    line-height: 80px;
  }
</style>
