<template>
  <div>
    <generic-error-message v-if="$fetchState.error" :error="$fetchState.error" />
    <picasso-period
      v-for="period in periods"
      v-else
      :key="period._meta.self"
      :period="period"
      :camp="camp"
      :orientation="options.orientation"
      :landscape="landscape"
      :index="index"
    />
  </div>
</template>

<script>
export default {
  name: 'ConfigPicasso',
  props: {
    options: { type: Object, required: false, default: null },
    camp: { type: Object, required: true },
    config: { type: Object, required: true },
    index: { type: Number, required: true },
  },
  data() {
    return {
      periods: [],
    }
  },
  async fetch() {
    await Promise.all([
      this.camp.periods().$loadItems(),
      this.camp.activities().$loadItems(),
      this.camp.categories().$loadItems(),
      this.camp
        .campCollaborations()
        .$loadItems()
        .then((campCollaborations) => {
          return Promise.all(
            campCollaborations.items.map((campCollaboration) => {
              return campCollaboration.user
                ? campCollaboration
                    .user()
                    ._meta.load.then((user) => user.profile()._meta.load)
                : Promise.resolve()
            })
          )
        }),
    ])

    this.periods = this.options.periods.map((periodUri) => {
      return this.$api.get(periodUri) // TODO prevent specifying arbitrary absolute URLs that the print container should fetch...
    })
  },
  computed: {
    landscape() {
      return this.options.orientation === 'L'
    },
  },
}
</script>
