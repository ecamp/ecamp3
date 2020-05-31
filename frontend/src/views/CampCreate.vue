<template>
  <v-container fluid>
    <content-card max-width="800">
      <v-toolbar>
        <v-toolbar-title>
          <ButtonBack />
          {{ $t('camp.create') }}
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-form ref="form" @submit.prevent="createCamp">
          <e-text-field
            v-model="camp.name"
            :label="$t('camp.name')"
            required />
          <e-text-field
            v-model="camp.title"
            :label="$t('camp.title')"
            required />
          <e-text-field
            v-model="camp.motto"
            :label="$t('camp.motto')"
            required />
          <e-select
            v-model="camp.campTypeId"
            :label="$t('camp.campType')"
            :items="campTypes" />
          <div class="text-right">
            <ButtonAdd type="submit">
              {{ $t('camp.create') }}
            </ButtonAdd>
          </div>
        </v-form>
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd'
import ButtonBack from '@/components/buttons/ButtonBack'
import ContentCard from '@/components/layout/ContentCard'
import ETextField from '@/components/form/base/ETextField'
import ESelect from '@/components/form/base/ESelect'
import { campRoute } from '@/router'

export default {
  name: 'Camps',
  components: {
    ESelect,
    ButtonBack,
    ButtonAdd,
    ContentCard,
    ETextField
  },
  data () {
    return {
      camp: {
        name: '',
        title: '',
        motto: '',
        campTypeId: null
      }
    }
  },
  computed: {
    campTypes () {
      return this.api.get('/camp-types').items.map(function (ct) {
        return {
          value: ct.id,
          text: this.$i18n.t(ct.name)
        }
      }.bind(this))
    }
  },
  created () {
  },
  methods: {
    createCamp: function () {
      this.api.post('/camps', this.camp).then(function (c) {
        this.$router.push(campRoute(c, 'admin'))
        this.api.get('/camps', true)
      }.bind(this))
    }
  }
}
</script>

<style scoped>

</style>
