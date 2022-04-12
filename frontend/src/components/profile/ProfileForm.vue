<template>
  <form>
    <api-text-field
      :name="$tc('entity.user.fields.email')"
      :uri="profile._meta.self"
      fieldname="email"
      :editing="false"
      required />
    <api-text-field
      :name="$tc('entity.user.fields.firstname')"
      :uri="profile._meta.self"
      fieldname="firstname" />
    <api-text-field
      :name="$tc('entity.user.fields.surname')"
      :uri="profile._meta.self"
      fieldname="surname" />
    <api-text-field
      :name="$tc('entity.user.fields.nickname')"
      :uri="profile._meta.self"
      fieldname="nickname" />
    <api-select
      :name="$tc('entity.user.fields.language')"
      :uri="profile._meta.self"
      fieldname="language"
      :items="availableLocales" />
    <p class="text-caption blue-grey--text mb-0">
      {{ $tc('global.lokaliseMessage') }}
    </p>
  </form>
</template>

<script>
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import VueI18n from '@/plugins/i18n'

export default {
  name: 'ProfileForm',
  components: {
    ApiSelect,
    ApiTextField
  },
  computed: {
    user () {
      return this.$auth.user()
    },
    profile () {
      return this.user.profile()
    },
    availableLocales () {
      return VueI18n.availableLocales.map(l => ({
        value: l,
        text: this.$tc('global.language', 1, l)
      }))
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
