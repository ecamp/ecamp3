import Vue from 'vue'
import Router from 'vue-router'
import { slugify } from '@/plugins/slugify.js'
import { isLoggedIn, isAdmin } from '@/plugins/auth'
import { apiStore } from '@/plugins/store'
import { campShortTitle } from '@/common/helpers/campShortTitle'
import { getEnv } from '@/environment.js'

Vue.use(Router)

const NavigationAuth = () => import('./views/auth/NavigationAuth.vue')
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
      path: '/debug',
      name: 'debug',
      components: {
        default: () => import('./views/admin/Debug.vue'),
      },
    },
    {
      path: '/admin',
      beforeEnter: all([requireAuth, requireAdmin]),
      components: {
        navigation: NavigationDefault,
        default: GenericPage,
        aside: () => import('./views/admin/SideBarAdmin.vue'),
      },
      children: [
        {
          path: '',
          redirect: 'debug',
        },
        {
          path: 'debug',
          name: 'admin/debug',
          components: {
            default: () => import('./views/admin/Debug.vue'),
          },
        },
        ...(getEnv().FEATURE_CHECKLIST
          ? [
              {
                path: 'checklists',
                name: 'admin/checklists',
                components: {
                  default: () => import('./views/admin/Checklists.vue'),
                },
              },
              {
                path: 'checklist/:checklistId/:checklistName?',
                name: 'admin/checklists/checklist',
                components: {
                  default: () => import('./views/admin/Checklist.vue'),
                },
                props: {
                  default: (route) => ({
                    checklist: checklistFromRoute(route),
                  }),
                },
              },
            ]
          : []),
      ],
    },

    {
      path: '/register',
      name: 'register',
      components: {
        navigation: NavigationAuth,
        default: () => import('./views/auth/Register.vue'),
      },
    },
    {
      path: '/register-done',
      name: 'register-done',
      components: {
        navigation: NavigationAuth,
        default: () => import('./views/auth/RegisterDone.vue'),
      },
    },
    {
      path: '/resend-activation',
      name: 'resendActivation',
      components: {
        navigation: NavigationAuth,
        default: () => import('./views/auth/ResendActivation.vue'),
      },
    },
    {
      path: '/reset-password',
      name: 'resetPasswordRequest',
      components: {
        navigation: NavigationAuth,
        default: () => import('./views/auth/ResetPasswordRequest.vue'),
      },
    },
    {
      path: '/reset-password/:id',
      name: 'resetPassword',
      components: {
        navigation: NavigationAuth,
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
        navigation: NavigationAuth,
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
        navigation: NavigationAuth,
        default: () => import('./views/auth/Login.vue'),
      },
    },
    {
      path: '/loginCallback',
      name: 'loginCallback',
      components: {
        navigation: NavigationAuth,
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
      path: '/invitations',
      name: 'invitations',
      components: {
        navigation: NavigationDefault,
        default: () => import('./views/Invitations.vue'),
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
      path: '/camps/:campId/:campShortTitle?',
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
          path: 'overview/checklists',
          name: 'camp/overview/checklists',
          component: () => import('./views/camp/checklistOverview/ChecklistLists.vue'),
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
      name: 'camp/material/all',
      path: '/camps/:campId/:campShortTitle?/material/all',
      components: {
        navigation: NavigationCamp,
        default: () => import('./views/camp/material/MaterialOverview.vue'),
        aside: () => import('./views/camp/material/SideBarMaterialLists.vue'),
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
      name: 'camp/overview/checklists/checklist',
      path: '/camps/:campId/:campShortTitle?/overview/checklists/:checklistId/:checklistName?',
      components: {
        navigation: NavigationCamp,
        default: () => import('./views/camp/checklistOverview/ChecklistOverview.vue'),
        aside: () =>
          import('./views/camp/checklistOverview/SideBarChecklistOverview.vue'),
      },
      beforeEnter: all([requireAuth, requireCamp]),
      props: {
        navigation: (route) => ({ camp: campFromRoute(route) }),
        aside: (route) => ({ camp: campFromRoute(route) }),
        default: (route) => ({
          camp: campFromRoute(route),
          checklist: checklistFromRoute(route),
        }),
      },
    },
    {
      name: 'camp/material/lists', // Only used on mobile
      path: '/camps/:campId/:campShortTitle?/material/lists',
      components: {
        navigation: NavigationCamp,
        default: () => import('./views/camp/material/MaterialLists.vue'),
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
      name: 'camp/material/detail',
      path: '/camps/:campId/:campShortTitle?/material/:materialId/:materialName?',
      components: {
        navigation: NavigationCamp,
        default: () => import('./views/camp/material/MaterialDetail.vue'),
        aside: () => import('./views/camp/material/SideBarMaterialLists.vue'),
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
      name: 'camp/admin/activity/category',
      path: '/camps/:campId/:campShortTitle?/category/:categoryId/:categoryName?',
      components: {
        navigation: NavigationCamp,
        default: () => import('./views/camp/category/Category.vue'),
        aside: () => import('./views/camp/category/SideBarCategory.vue'),
      },
      beforeEnter: all([requireAuth, requireCamp, requireCategory]),
      props: {
        navigation: (route) => ({ camp: campFromRoute(route) }),
        aside: (route) => ({ camp: campFromRoute(route) }),
        default: (route) => ({
          camp: campFromRoute(route),
          category: categoryFromRoute(route),
        }),
      },
    },
    ...(getEnv().FEATURE_CHECKLIST
      ? [
          // Checklist-Pages:
          {
            name: 'camp/admin/checklists/checklist',
            path: '/camps/:campId/:campTitle?/admin/checklist/:checklistId/:checklistName?',
            components: {
              navigation: NavigationCamp,
              default: () => import('./views/camp/checklist/Checklist.vue'),
              aside: () => import('./views/camp/checklist/SideBarChecklist.vue'),
            },
            beforeEnter: all([requireAuth, requireCamp, requireChecklist]),
            props: {
              navigation: (route) => ({ camp: campFromRoute(route) }),
              aside: (route) => ({ camp: campFromRoute(route) }),
              default: (route) => ({
                camp: campFromRoute(route),
                checklist: checklistFromRoute(route),
              }),
            },
          },
        ]
      : []),
    {
      path: '/camps/:campId/:campShortTitle?/admin',
      components: {
        navigation: NavigationCamp,
        default: GenericPage,
        aside: () => import('./views/camp/admin/SideBarAdmin.vue'),
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
          name: 'camp/admin/info',
          component: () => import('./views/camp/admin/Info.vue'),
          props: (route) => ({ camp: campFromRoute(route) }),
        },
        {
          path: 'activity',
          name: 'camp/admin/activity',
          component: () => import('./views/camp/admin/Activity.vue'),
          props: (route) => ({ camp: campFromRoute(route) }),
        },
        {
          path: 'collaborators',
          name: 'camp/admin/collaborators',
          component: () => import('./views/camp/admin/Collaborators.vue'),
          props: () => ({ layout: 'normal' }),
        },
        {
          path: 'material',
          name: 'camp/admin/material',
          component: () => import('./views/camp/admin/AdminMaterialLists.vue'),
        },
        {
          path: 'print',
          name: 'camp/admin/print',
          component: () => import('./views/camp/admin/Print.vue'),
          props: (route) => ({ camp: campFromRoute(route) }),
        },
        ...(getEnv().FEATURE_CHECKLIST
          ? [
              // Checklist-Pages:
              {
                path: 'checklists',
                name: 'camp/admin/checklists',
                component: () => import('./views/camp/admin/Checklists.vue'),
                props: (route) => ({ camp: campFromRoute(route) }),
              },
            ]
          : []),
        {
          path: 'materiallists',
          name: 'camp/material',
          redirect: { name: 'camp/admin/material' },
        },
        {
          path: '',
          name: 'camp/admin',
          redirect: { name: 'camp/admin/info' },
        },
      ],
    },
    {
      path: '/camps/:campId/:campShortTitle/program/activities/:scheduleEntryId/:activityName?',
      name: 'camp/activity',
      components: {
        navigation: NavigationCamp,
        default: () => import('./views/camp/activity/Activity.vue'),
        aside: () => import('./views/camp/activity/SideBarProgram.vue'),
      },
      beforeEnter: all([requireAuth, requireCamp, requireScheduleEntry]),
      props: {
        navigation: (route) => ({ camp: campFromRoute(route) }),
        default: (route) => ({ scheduleEntry: scheduleEntryFromRoute(route) }),
        aside: (route) => ({
          camp: campFromRoute(route),
          day: dayFromScheduleEntryInRoute(route),
        }),
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

function requireAdmin(to, from, next) {
  if (isAdmin()) {
    next()
  } else {
    next({
      name: 'PageNotFound',
      params: [to.fullPath, ''],
      replace: true,
    })
  }
}

async function requireCamp(to, from, next) {
  const camp = await campFromRoute(to)
  if (camp === undefined) {
    next({
      name: 'PageNotFound',
      params: [to.fullPath, ''],
      replace: true,
    })
  } else {
    await camp._meta.load
      .then(() => {
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
}

async function requireScheduleEntry(to, from, next) {
  const scheduleEntry = await scheduleEntryFromRoute(to)
  if (scheduleEntry === undefined) {
    next({
      name: 'PageNotFound',
      params: [to.fullPath, ''],
      replace: true,
    })
  } else {
    await scheduleEntry._meta.load
      .then(() => {
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
}

async function requirePeriod(to, from, next) {
  const period = await periodFromRoute(to)
  if (period === undefined) {
    next({
      name: 'PageNotFound',
      params: [to.fullPath, ''],
      replace: true,
    })
  } else {
    await period._meta.load
      .then(() => {
        next()
      })
      .catch(() => {
        next(campRoute(campFromRoute(to)))
      })
  }
}

async function requireCategory(to, from, next) {
  const category = await categoryFromRoute(to)
  if (category === undefined) {
    next({
      name: 'PageNotFound',
      params: [to.fullPath, ''],
      replace: true,
    })
  } else {
    await category._meta.load
      .then(() => {
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
}

async function requireMaterialList(to, from, next) {
  const materialList = await materialListFromRoute(to)
  if (materialList === undefined) {
    next({
      name: 'PageNotFound',
      params: [to.fullPath, ''],
      replace: true,
    })
  } else {
    await materialList._meta.load
      .then(() => {
        next()
      })
      .catch(() => {
        next(campRoute(campFromRoute(to)))
      })
  }
}

async function requireChecklist(to, from, next) {
  const checklist = await checklistFromRoute(to)
  if (checklist === undefined) {
    next({
      name: 'PageNotFound',
      params: [to.fullPath, ''],
      replace: true,
    })
  } else {
    await checklist._meta.load
      .then(() => {
        next()
      })
      .catch(() => {
        next(campRoute(campFromRoute(to)))
      })
  }
}

export function campFromRoute(route) {
  return apiStore.get().camps({ id: route.params.campId })
}

export function invitationFromInviteKey(inviteKey) {
  return apiStore.get().invitations({ action: 'find', id: inviteKey })
}

export function periodFromRoute(route) {
  return apiStore.get().periods({ id: route.params.periodId })
}

function scheduleEntryFromRoute(route) {
  return apiStore.get().scheduleEntries({ id: route.params.scheduleEntryId })
}

function categoryFromRoute(route) {
  return campFromRoute(route)
    .categories()
    .allItems.find((c) => c.id === route.params.categoryId)
}

export function materialListFromRoute(route) {
  return apiStore.get().materialLists({ id: route.params.materialId })
}

export function checklistFromRoute(route) {
  return campFromRoute(route)
    .checklists()
    .allItems.find((c) => c.id === route.params.checklistId)
}

function getContentLayout(route) {
  switch (route.name) {
    case 'camp/checklist':
    case 'camp/period/program':
    case 'camp/admin/print':
    case 'camp/admin/activity/category':
      return 'full'
    case 'camp/print':
    case 'camp/material':
    case 'camp/admin/info':
    case 'camp/admin/activity':
      return 'wide'
    case 'camp/admin/collaborators':
    case 'camp/admin/material':
    default:
      return 'normal'
  }
}

function dayFromScheduleEntryInRoute(route) {
  return apiStore.get().scheduleEntries({ id: route.params.scheduleEntryId }).day()
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
    params: { campId: camp.id, campShortTitle: slugify(campShortTitle(camp)) },
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
      name: `camp/material${materialListOrRoute}`,
      params: { campId: camp.id, campShortTitle: slugify(campShortTitle(camp)) },
      query,
    }
  }
  if (!materialListOrRoute?._meta || materialListOrRoute.meta?.loading) return {}
  return {
    name: 'camp/material/detail',
    params: {
      campId: camp.id,
      campShortTitle: slugify(campShortTitle(camp)),
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
    name: 'camp/admin/' + subroute,
    params: { campId: camp.id, campShortTitle: slugify(campShortTitle(camp)) },
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
      campShortTitle: slugify(campShortTitle(camp)),
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
    name: 'camp/activity',
    params: {
      campId: camp.id,
      campShortTitle: slugify(campShortTitle(camp)),
      scheduleEntryId: scheduleEntry.id,
      activityName: slugify(scheduleEntry.activity().title),
    },
    query,
  }
}

export function categoryRoute(camp, category, query = {}) {
  if (camp._meta.loading || category._meta.loading) return {}
  return {
    name: 'camp/admin/activity/category',
    params: {
      campId: camp.id,
      campShortTitle: slugify(campShortTitle(camp)),
      categoryId: category.id,
      categoryName: slugify(category.name),
    },
    query,
  }
}

export function checklistRoute(camp, checklist, query = {}) {
  if (camp?._meta.loading || checklist?._meta.loading) return {}

  if (!camp) {
    if (!checklist) {
      return {
        name: 'admin/checklists',
        query,
      }
    }
    return {
      name: 'admin/checklists/checklist',
      params: {
        checklistId: checklist.id,
        checklistName: slugify(checklist.name),
      },
      query,
    }
  }

  if (!checklist) {
    return {
      name: 'camp/admin/checklists',
      params: {
        campId: camp.id,
        campTitle: slugify(camp.title),
      },
      query,
    }
  }
  return {
    name: 'camp/admin/checklists/checklist',
    params: {
      campId: camp.id,
      campTitle: slugify(camp.title),
      checklistId: checklist.id,
      checklistName: slugify(checklist.name),
    },
    query,
  }
}

export function checklistOverviewRoute(camp, checklist, query = {}) {
  if (camp?._meta.loading || checklist._meta.loading) return {}

  if (!checklist) {
    return {
      name: 'camp/overview/checklists',
      params: {
        campId: camp.id,
        campTitle: slugify(camp.title),
      },
      query,
    }
  }

  return {
    name: 'camp/overview/checklists/checklist',
    params: {
      campId: camp.id,
      campTitle: slugify(camp.title),
      checklistId: checklist.id,
      checklistName: slugify(checklist.name),
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
    next(campRoute(camp, 'camp/admin', to.query))
  }
}
