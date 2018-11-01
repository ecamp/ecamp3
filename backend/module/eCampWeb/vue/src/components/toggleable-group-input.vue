<!--
Displays a group as text or as an dropdown selection field, depending on the editing prop.
You can two-way bind to the value using v-model.
TODO: Fix bug causing the dropdown to be blank when closed
-->

<template>
  <span>
    <span v-if="!editing">{{ fieldname }}: {{ value.name }}</span>
    <span v-if="editing">{{ fieldname }}:
      <select
        v-model="valueModel"
        class="form-control">
        <option
          v-for="group in allGroups"
          :key="group.id"
          :value="group.id"
          :selected="group.id === valueModel.id">
          {{ group.name }}
        </option>
      </select>
    </span>
  </span>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ToggleableGroupInput',
  props: {
    editing: { type: Boolean, default: false },
    fieldname: { type: String, default: '' },
    value: { type: String, required: true }
  },
  data () {
    return {
      allGroups: []
    }
  },
  computed: {
    valueModel: {
      get () {
        return this.getGroup(this.value.id)
      },
      set (newValue) {
        this.$emit('input', this.getGroup(newValue))
      }
    }
  },
  created () {
    this.fetchFromAPI()
  },
  methods: {
    async fetchFromAPI () {
      try {
        this.allGroups = (await axios.get('/api/group')).data._embedded.items
      } catch (error) {
        this.$emit('error', [{ type: 'danger', text: 'Could not get group list. ' + error }])
      }
    },
    getGroup (id) {
      return this.allGroups.find(function (group) {
        return group.id === id
      })
    }
  }
}
</script>

<style scoped>

</style>
