<template>
  <div class="mb-3">
    <v-simple-table dense>
      <thead>
        <tr>
          <th class="text-left" style="width: 10%;">
            {{ $tc("entity.materialItem.fields.quantity") }}
          </th>
          <th style="width: 15%" />
          <th class="text-left">
            {{ $tc("entity.materialItem.fields.article") }}
          </th>
          <th class="text-left" style="width: 20%;">
            {{ $tc('entity.materialList.name') }}
          </th>
          <th class="text-left" style="width: 15%;">
            Option
          </th>
        </tr>
      </thead>
      <tbody>
        <template v-for="materialItem in materialItemsSorted">
          <tr v-if="materialItem._meta != undefined" :key="materialItem.id">
            <td>
              <api-text-field
                dense
                :outlined="false"
                :uri="materialItem._meta.self"
                fieldname="quantity" />
            </td>
            <td>
              <api-text-field
                dense
                :outlined="false"
                :uri="materialItem._meta.self"
                fieldname="unit" />
            </td>
            <td>
              <api-text-field
                dense
                :outlined="false"
                :uri="materialItem._meta.self"
                fieldname="article" />
            </td>
            <td>
              <api-select
                dense
                :outlined="false"
                :uri="materialItem._meta.self"
                relation="materialList"
                fieldname="materialListId"
                :items="materialLists" />
            </td>
            <td>
              <a href="#" @click="deleteMaterialItem(materialItem)">
                {{ $tc('global.button.delete') }}
              </a>
            </td>
          </tr>
          <tr v-else :key="materialItem.id">
            <td>{{ materialItem.quantity }}</td>
            <td>{{ materialItem.unit }}</td>
            <td>{{ materialItem.article }}</td>
            <td />
            <td />
          </tr>
        </template>
      </tbody>
    </v-simple-table>

    <material-create-item
      :camp="camp"
      :activity-content="activityContent"
      @item-adding="onItemAdding" />
  </div>
</template>

<script>
import ApiTextField from '../../form/api/ApiTextField.vue'
import ApiSelect from '../../form/api/ApiSelect.vue'
import MaterialCreateItem from '../../camp/MaterialCreateItem.vue'

export default {
  name: 'Material',
  components: {
    ApiTextField,
    ApiSelect,
    MaterialCreateItem
  },
  props: {
    activityContent: { type: Object, required: true }
  },
  data () {
    return {
      newMaterialItems: {}
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
      return this.api.get().materialItems({ activityContentId: this.activityContent.id })
    },
    materialItemsSorted () {
      const items = this.materialItems.items

      // eager add new Items
      for (var key in this.newMaterialItems) {
        var mi = this.newMaterialItems[key]
        items.push({
          id: key,
          quantity: mi.quantity,
          unit: mi.unit,
          article: mi.article
        })
      }

      return items.sort((a, b) => a.article.localeCompare(b.article))
    }
  },
  methods: {
    materialListItems (materialList) {
      return this.materialItems.filter(mi => mi.materialList().id === materialList.id)
    },
    deleteMaterialItem (materialItem) {
      this.api.del(materialItem)
    },
    onItemAdding (key, data, res) {
      this.$set(this.newMaterialItems, key, data)

      res.then(mi => {
        this.api.reload(this.materialItems).then(() => {
          this.$delete(this.newMaterialItems, key)
        })
      })
    }
  }
}
</script>

<style scoped>
</style>
