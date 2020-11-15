<template>
  <div class="mb-3">
    <div v-for="materialList in materialLists.items" :key="materialList.id">
      <h2>{{ materialList.name }}</h2>
    </div>
    <v-row v-for="materialItem in materialItems" :key="materialItem.id"
           dense
           no-glutters justify="space-around">
      <v-col>
        <api-text-field
          name="Artikel"
          :uri="materialItem._meta.self"
          fieldname="article" />
      </v-col>
      <v-col>
        <api-text-field
          name="Quantity"
          :uri="materialItem._meta.self"
          fieldname="quantity" />
      </v-col>
      <v-col>
        <api-text-field
          name="Unit"
          :uri="materialItem._meta.self"
          fieldname="unit" />
      </v-col>
      <v-col>
        <api-select
          name="List"
          :uri="materialItem._meta.self"
          relation="materialList"
          fieldname="materialListId"
          :items="materialLists" />
      </v-col>
      <v-col>
        <a href="#" @click="deleteMaterialItem(materialItem)">
          Delete
        </a>
      </v-col>
    </v-row>
    <v-row dense no-glutters justify="space-around">
      <v-col>
        <e-text-field
          v-model="newMaterialItem.article"
          name="Artikel"
          fieldname="article" />
      </v-col>
      <v-col>
        <e-text-field
          v-model="newMaterialItem.quantity"
          name="Quantity"
          fieldname="quantity" />
      </v-col>
      <v-col>
        <e-text-field
          v-model="newMaterialItem.unit"
          name="Unit"
          fieldname="unit" />
      </v-col>
      <v-col>
        <e-select
          v-model="newMaterialItem.materialListId"
          name="List"
          fieldname="materialListId"
          :items="materialLists" />
      </v-col>
      <v-col>
        <a href="#" @click="createMaterialItem">
          SAVE
        </a>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import ApiTextField from '../../form/api/ApiTextField.vue'
import ApiSelect from '../../form/api/ApiSelect.vue'
import ETextField from '../../form/base/ETextField.vue'
import ESelect from '../../form/base/ESelect.vue'

export default {
  name: 'Material',
  components: {
    ApiTextField,
    ApiSelect,
    ETextField,
    ESelect
  },
  props: {
    activityContent: { type: Object, required: true }
  },
  data () {
    return {
      newMaterialItem: {}
    }
  },
  computed: {
    camp () {
      return this.activityContent.activity().camp()
    },
    materialLists () {
      return this.camp.materialLists().items.map(l => ({
        value: l.id,
        text: l.name
      }))
    },
    materialItems () {
      return this.api.get().materialItems({ activityContentId: this.activityContent.id }).items
        .sort((a, b) => a.article.localeCompare(b.article))
    }
  },
  mounted () {
    this.resetMaterialItem()
  },
  methods: {
    materialListItems (materialList) {
      return this.materialItems.filter(mi => mi.materialList().id === materialList.id)
    },
    createMaterialItem () {
      this.api.href(this.api.get(), 'materialItems').then(uri => {
        this.api.post(uri, this.newMaterialItem)
      })
    },
    deleteMaterialItem (materialItem) {
      this.api.del(materialItem)
    },
    resetMaterialItem () {
      this.newMaterialItem = {
        article: '',
        quantity: null,
        unit: '',
        materialListId: null,
        activityContentId: this.activityContent.id
      }
    }
  }
}
</script>

<style scoped>
</style>
