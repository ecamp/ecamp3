import Vue from 'vue'
import Router from 'vue-router'
import { slugify } from '@/plugins/slugify.js'
import { isLoggedIn } from '@/plugins/auth'
import { apiStore } from '@/plugins/store'
import { getEnv } from '@/environment.js'

Vue.use(Router)

const NavigationDefault = () => import('./views/NavigationDefault.vue')
const NavigationCamp = () => import('./views/camp/navigation/NavigationCamp.vue')
const GenericPage = () => import('./components/generic/GenericPage.vue')

/* istanbul ignore next */
export default new Router({
  mode: 'history',
  base: '/',
  routes: [
    ...(getEnv().FEATURE_DEVELOPER
      ? [
          // Dev-Pages:
          {
            path: '/controls',
            name: 'controls',
            components: {
              default: () => import('./views/dev/Controls.vue'),
            },
            beforeEnter: requireAuth,
          },
          {
            path: '/performance',
            name: 'performance',
            components: {
              default: () => import('./views/dev/Performance.vue'),
            },
            beforeEnter: requireAuth,
          },
        ]
      : []),

    // Prod-Pages:
    {
      path: '/register',
      name: 'register',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/auth/Register.vue'),
      },
    },
    {
      path: '/register-done',
      name: 'register-done',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/auth/RegisterDone.vue'),
      },
    },
    {
      path: '/reset-password',
      name: 'resetPasswordRequest',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/auth/ResetPasswordRequest.vue'),
      },
    },
    {
      path: '/reset-password/:id',
      name: 'resetPassword',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/auth/ResetPassword.vue'),
      },
      props: {
        default: (route) => {
          return {
            id: route.params.id,
          }
        },
      },
    },
    {
      path: '/activate/:userId/:activationKey',
      name: 'activate',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/auth/Activate.vue'),
      },
      props: {
        default: (route) => {
          return {
            userId: route.params.userId,
            activationKey: route.params.activationKey,
          }
        },
      },
    },
    {
      path: '/login',
      name: 'login',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/auth/Login.vue'),
      },
    },
    {
      path: '/loginCallback',
      name: 'loginCallback',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/auth/LoginCallback.vue'),
      },
    },
    {
      path: '/profile',
      name: 'profile',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/Profile.vue'),
      },
      beforeEnter: requireAuth,
    },
    {
      path: '/profile/verify-mail/:emailVerificationKey',
      name: 'profileVerifyEmail',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/Profile.vue'),
      },
      props: {
        default: (route) => {
          return { emailVerificationKey: route.params.emailVerificationKey }
        },
      },
      beforeEnter: requireAuth,
    },
    {
      path: '/camps',
      name: 'camps',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/Camps.vue'),
      },
      beforeEnter: requireAuth,
    },
    {
      path: '/camps/create',
      name: 'camps/create',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/CampCreate.vue'),
      },
      beforeEnter: requireAuth,
    },
    {
      path: '/camps/invitation/rejected',
      name: 'invitationRejected',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/camp/Invitation.vue'),
      },
      props: {
        default: () => ({
          variant: 'rejected',
        }),
      },
    },
    {
      path: '/camps/invitation/updateError',
      name: 'invitationUpdateError',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/camp/Invitation.vue'),
      },
      props: {
        default: () => ({
          variant: 'error',
        }),
      },
    },
    {
      path: '/camps/invitation/:inviteKey',
      name: 'campInvitation',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/camp/Invitation.vue'),
      },
      props: {
        default: (route) => ({
          variant: 'default',
          invitation: invitationFromInviteKey(route.params.inviteKey),
        }),
      },
    },
    {
      path: '/camps/:campId/:campTitle?',
      components: {
        navigation: NavigationCamp,
        default: GenericPage,
      },
      beforeEnter: all([requireAuth, requireCamp]),
      props: {
        navigation: (route) => ({ camp: campFromRoute(route) }),
        default: (route) => ({
          camp: campFromRoute(route),
          period: periodFromRoute(route),
          layout: getContentLayout(route),
        }),
      },
      children: [
        {
          path: 'program/period/:periodId/:periodTitle?',
          name: 'camp/period/program',
          component: () => import('./views/camp/CampProgram.vue'),
          beforeEnter: requirePeriod,
        },
        {
          path: 'program',
          name: 'camp/program',
          async beforeEnter(to, from, next) {
            redirectToPeriod(to, from, next, 'camp/period/program')
          },
        },
        {
          path: 'story/period/:periodId/:periodTitle?',
          name: 'camp/period/story',
          component: () => import('./views/camp/Story.vue'),
          beforeEnter: requirePeriod,
        },
        {
          path: 'story',
          name: 'camp/story',
          async beforeEnter(to, from, next) {
            redirectToPeriod(to, from, next, 'camp/period/story')
          },
        },
        {
          path: 'dashboard',
          name: 'camp/dashboard',
          component: () => import('./views/camp/Dashboard.vue'),
        },
        {
          path: '',
          name: 'camp/home',
          redirect: { name: 'camp/dashboard' },
        },
      ],
    },
    {
      name: 'material/all',
      path: '/camps/:campId/:campTitle?/material/all',
      components: {
        navigation: NavigationCamp,
        default: () => import('./views/material/MaterialOverview.vue'),
        aside: () => import('./views/material/SideBarMaterialLists.vue'),
      },
      beforeEnter: all([requireAuth, requireCamp]),
      props: {
        navigation: (route) => ({ camp: campFromRoute(route) }),
        aside: (route) => ({ camp: campFromRoute(route) }),
        default: (route) => ({
          camp: campFromRoute(route),
        }),
      },
    },
    {
      name: 'material/lists', // Only used on mobile
      path: '/camps/:campId/:campTitle?/material/lists',
      components: {
        navigation: NavigationCamp,
        default: () => import('./views/material/MaterialLists.vue'),
      },
      beforeEnter: all([requireAuth, requireCamp]),
      props: {
        navigation: (route) => ({ camp: campFromRoute(route) }),
        default: (route) => ({
          camp: campFromRoute(route),
        }),
      },
    },
    {
      name: 'material/detail',
      path: '/camps/:campId/:campTitle?/material/:materialId/:materialName?',
      components: {
        navigation: NavigationCamp,
        default: () => import('./views/material/MaterialDetail.vue'),
        aside: () => import('./views/material/SideBarMaterialLists.vue'),
      },
      beforeEnter: all([requireAuth, requireCamp, requireMaterialList]),
      props: {
        navigation: (route) => ({ camp: campFromRoute(route) }),
        aside: (route) => ({ camp: campFromRoute(route) }),
        default: (route) => ({
          camp: campFromRoute(route),
          materialList: materialListFromRoute(route),
        }),
      },
    },
    {
      path: '/camps/:campId/:campTitle?/admin',
      components: {
        navigation: NavigationCamp,
        default: GenericPage,
        aside: () => import('./views/admin/SideBarAdmin.vue'),
      },
      beforeEnter: all([requireAuth, requireCamp]),
      props: {
        navigation: (route) => ({ camp: campFromRoute(route) }),
        default: (route) => ({
          camp: campFromRoute(route),
          layout: getContentLayout(route),
        }),
        aside: (route) => ({ camp: campFromRoute(route) }),
      },
      children: [
        {
          path: 'info',
          name: 'admin/info',
          component: () => import('./views/admin/Info.vue'),
          props: (route) => ({ camp: campFromRoute(route) }),
        },
        {
          path: 'activity',
          name: 'admin/activity',
          component: () => import('./views/admin/Activity.vue'),
          props: (route) => ({ camp: campFromRoute(route) }),
        },
        {
          path: 'category/:categoryId/:categoryName?',
          name: 'admin/activity/category',
          component: () => import('./views/activity/Category.vue'),
          props: (route) => ({ category: categoryFromRoute(route) }),
        },
        {
          path: 'collaborators',
          name: 'admin/collaborators',
          component: () => import('./views/admin/Collaborators.vue'),
          props: () => ({ layout: 'normal' }),
        },
        {
          path: 'material',
          name: 'admin/material',
          component: () => import('./views/admin/AdminMaterialLists.vue'),
        },
        {
          path: 'print',
          name: 'admin/print',
          component: () => import('./views/admin/Print.vue'),
          props: (route) => ({ camp: campFromRoute(route) }),
        },
        {
          path: 'materiallists',
          name: 'camp/material',
          redirect: { name: 'admin/material' },
        },
        {
          path: '',
          name: 'camp/admin',
          redirect: { name: 'admin/info' },
        },
      ],
    },
    {
      path: '/camps/:campId/:campTitle/program/activities/:scheduleEntryId/:activityName?',
      name: 'activity',
      components: {
        navigation: NavigationCamp,
        default: () => import('./views/activity/Activity.vue'),
        aside: () => import('./views/activity/SideBarProgram.vue'),
      },
      beforeEnter: requireAuth,
      props: {
        navigation: (route) => ({ camp: campFromRoute(route) }),
        default: (route) => ({ scheduleEntry: scheduleEntryFromRoute(route) }),
        aside: (route) => ({ day: dayFromScheduleEntryInRoute(route) }),
      },
    },
    {
      path: '/',
      name: 'home',
      redirect: { name: 'camps' },
    },
    {
      path: '**',
      name: 'PageNotFound',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/PageNotFound.vue'),
      },
    },
  ],
})

function evaluateGuards(guards, to, from, next) {
  const guardsLeft = guards.slice(0)
  const nextGuard = guardsLeft.shift()

  if (nextGuard === undefined) {
    next()
    return
  }

  nextGuard(to, from, (nextArg) => {
    if (nextArg === undefined) {
      evaluateGuards(guardsLeft, to, from, next)
      return
    }
    next(nextArg)
  })
}

function all(guards) {
  return (to, from, next) => evaluateGuards(guards, to, from, next)
}

function requireAuth(to, from, next) {
  if (isLoggedIn()) {
    next()
  } else {
    next({ name: 'login', query: to.path === '/' ? {} : { redirect: to.fullPath } })
  }
}

async function requireCamp(to, from, next) {
  await campFromRoute(to)
    .call({ api: { get: apiStore.get } })
    ._meta.load.then(() => {
      next()
    })
    .catch(() => {
      next({
        name: 'PageNotFound',
        params: [to.fullPath, ''],
        replace: true,
      })
    })
}

async function requirePeriod(to, from, next) {
  await periodFromRoute(to)
    .call({ api: { get: apiStore.get } })
    ._meta.load.then(() => {
      next()
    })
    .catch(() => {
      next(campRoute(campFromRoute(to).call({ api: { get: apiStore.get } })))
    })
}

async function requireMaterialList(to, from, next) {
  await materialListFromRoute(to)
    .call({ api: { get: apiStore.get } })
    ._meta.load.then(() => {
      next()
    })
    .catch(() => {
      next(campRoute(campFromRoute(to).call({ api: { get: apiStore.get } })))
    })
}

export function campFromRoute(route) {
  return function () {
    return this.api.get().camps({ id: route.params.campId })
  }
}

export function invitationFromInviteKey(inviteKey) {
  return function () {
    return this.api.get().invitations({ action: 'find', id: inviteKey })
  }
}

export function periodFromRoute(route) {
  return function () {
    return this.api.get().periods({ id: route.params.periodId })
  }
}

function scheduleEntryFromRoute(route) {
  return function () {
    return this.api.get().scheduleEntries({ id: route.params.scheduleEntryId })
  }
}

function categoryFromRoute(route) {
  return function () {
    const camp = this.api.get().camps({ id: route.params.campId })
    return camp.categories().items.find((c) => c.id === route.params.categoryId)
  }
}

export function materialListFromRoute(route) {
  return function () {
    return this.api.get().materialLists({ id: route.params.materialId })
  }
}

function getContentLayout(route) {
  switch (route.name) {
    case 'camp/period/program':
    case 'admin/print':
    case 'admin/activity/category':
      return 'full'
    case 'camp/print':
    case 'camp/material':
    case 'admin/info':
    case 'admin/activity':
      return 'wide'
    case 'admin/collaborators':
    case 'admin/material':
    default:
      return 'normal'
  }
}

function dayFromScheduleEntryInRoute(route) {
  return function () {
    return this.api.get().scheduleEntries({ id: route.params.scheduleEntryId }).day()
  }
}

/**
 * @param camp
 * @param subroute {'admin' | 'dashboard' | 'program' | 'material' | 'story' | 'home' | 'collaborators' | 'print' }
 * @param query
 */
export function campRoute(camp, subroute = 'dashboard', query = {}) {
  if (camp._meta.loading) return {}
  return {
    name: 'camp/' + subroute,
    params: { campId: camp.id, campTitle: slugify(camp.title) },
    query,
  }
}

/**
 * @param camp
 * @param materialListOrRoute { '/all', '/lists', object }
 * @param query
 */
export function materialListRoute(camp, materialListOrRoute = '/all', query = {}) {
  if (camp._meta.loading) return {}
  if (typeof materialListOrRoute === 'string') {
    return {
      name: `material${materialListOrRoute}`,
      params: { campId: camp.id, campTitle: slugify(camp.title) },
      query,
    }
  }
  if (!materialListOrRoute?._meta || materialListOrRoute.meta?.loading) return {}
  return {
    name: 'material/detail',
    params: {
      campId: camp.id,
      campTitle: slugify(camp.title),
      materialId: materialListOrRoute.id,
      materialName: slugify(materialListOrRoute.name),
    },
    query,
  }
}

/**
 * @param camp
 * @param subroute {'info' | 'activity' | 'collaborators' | 'material' | 'print'}
 * @param query
 */
export function adminRoute(camp, subroute = 'info', query = {}) {
  if (camp._meta.loading) return {}
  return {
    name: 'admin/' + subroute,
    params: { campId: camp.id, campTitle: slugify(camp.title) },
    query,
  }
}

export function loginRoute(redirectTo) {
  return { path: '/login', query: { redirect: redirectTo } }
}

export function periodRoute(period, routeName = 'camp/period/program', query = {}) {
  const camp = period.camp()
  if (camp._meta.loading || period._meta.loading) return {}
  return {
    name: routeName,
    params: {
      campId: camp.id,
      campTitle: slugify(camp.title),
      periodId: period.id,
      periodTitle: slugify(period.description),
    },
    query,
  }
}

export function scheduleEntryRoute(scheduleEntry, query = {}) {
  if (scheduleEntry._meta.loading || scheduleEntry.activity()._meta.loading) return {}

  const camp = scheduleEntry.activity().camp()

  // if (camp._meta.loading) return {}

  return {
    name: 'activity',
    params: {
      campId: camp.id,
      campTitle: slugify(camp.title),
      scheduleEntryId: scheduleEntry.id,
      activityName: slugify(scheduleEntry.activity().title),
    },
    query,
  }
}

export function categoryRoute(camp, category, query = {}) {
  if (camp._meta.loading || category._meta.loading) return {}
  return {
    name: 'admin/activity/category',
    params: {
      campId: camp.id,
      campTitle: slugify(camp.title),
      categoryId: category.id,
      categoryName: slugify(category.name),
    },
    query,
  }
}

async function firstFuturePeriod(route) {
  const periods = await apiStore.get().camps({ id: route.params.campId }).periods()._meta
    .load
  // Return the first period that hasn't ended, or if no such period exists, return the first period
  return (
    periods.items.find((period) => new Date(period.end) >= new Date()) ||
    periods.items.find((_) => true)
  )
}

async function redirectToPeriod(to, from, next, routeName) {
  const period = await firstFuturePeriod(to)
  if (period) {
    await period.camp()._meta.load
    next(periodRoute(period, routeName, to.query))
  } else {
    const camp = await apiStore.get().camps({ id: to.params.campId })
    next(campRoute(camp, 'admin', to.query))
  }
}
