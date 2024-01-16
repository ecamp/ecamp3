<template>
  <!--  <ValidationObserver-->
  <!--    v-if="materialLists.length > 0"-->
  <!--    ref="validation"-->
  <!--    tag="tr"-->
  <!--    class="newItemRow"-->
  <!--    @keyup.enter="submitForm"-->
  <!--  >-->
  <tr v-if="materialLists.length > 0" class="pt-1">
    <td>
      <e-text-field
        ref="quantity"
        v-model.number="materialItem.quantity"
        dense
        inputmode="numeric"
        :name="$tc('entity.materialItem.fields.quantity')"
        fieldname="quantity"
      />
    </td>
    <td class="pt-1">
      <e-text-field
        v-model="materialItem.unit"
        dense
        :name="$tc('entity.materialItem.fields.unit')"
        fieldname="unit"
        maxlength="32"
      />
    </td>
    <td class="pt-1">
      <e-text-field
        v-model="materialItem.article"
        dense
        vee-rules="required"
        :name="$tc('entity.materialItem.fields.article')"
        fieldname="article"
        maxlength="64"
      />
    </td>
    <td class="pt-1" :colspan="columns - 4">
      <e-select
        v-model="materialItem.materialList"
        dense
        vee-rules="required"
        :name="$tc('entity.materialList.name')"
        fieldname="materialList"
        :items="materialLists"
      />
    </td>
    <td class="pt-1">
      <button-add height="52" hide-label @click="submitForm" />
    </td>
  </tr>
  <!--  </ValidationObserver>-->
  <tr v-else>
    <td :colspan="columns">
      <div>
        <p>
          {{ $tc('components.material.materialCreateItem.noMaterialListAvailable') }}
        </p>
        <v-btn :to="campRoute(camp, 'admin')">
          <v-icon :start="$vuetify.display.mdAndUp">mdi-cogs</v-icon>
          {{ $tc('components.material.materialCreateItem.campSettingsButton') }}
        </v-btn>
      </div>
    </td>
  </tr>
</template>

<script>
import { campRoute } from '@/router.js'
// import { ValidationObserver } from 'vee-validate'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'

export default {
  name: 'MaterialCreateItem',
  components: {
    // ValidationObserver,
    ButtonAdd,
  },
  props: {
    camp: { type: Object, required: true },

    materialList: { type: Object, required: false, default: null },

    /* number of colums currently visible in table */
    columns: { type: Number, required: true },
  },
  data() {
    return {
      materialItem: {},
    }
  },
  computed: {
    materialLists() {
      return this.camp.materialLists().items.map((list) => ({
        value: list._meta.self,
        text: list.name,
      }))
    },
  },
  created() {
    this.initEntity()
  },
  methods: {
    initEntity() {
      this.materialItem = {
        materialList: this.materialList?._meta.self ?? undefined,
      }
    },
    async submitForm() {
      const isValid = await this.$refs.validation.validate()
      if (isValid) {
        this.createMaterialItem()
      }
    },
    createMaterialItem() {
      const key = Date.now()
      const data = this.materialItem

      this.initEntity()
      this.$refs.validation.reset()
      this.$refs.quantity.focus()

      // fire event to allow for eager adding before post has finished
      this.$emit('item-adding', key, data)
    },
    campRoute,
  },
}
</script>

<style scoped>
.newItemRow {
  line-height: 80px;
  vertical-align: top;
}
.v-btn {
  vertical-align: text-bottom;
}
</style>
