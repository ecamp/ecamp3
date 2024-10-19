import http from 'k6/http'
import { Trend } from 'k6/metrics'

export const options = {
  // A number specifying the number of VUs to run concurrently.
  vus: __ENV.VUS || 10,
  iterations: __ENV.ITERATIONS || 100,
  // A string specifying the total duration of the test run.
  duration: __ENV.DURATION,
}

const host = __ENV.API_ROOT_URL || 'http://localhost:3000'

const specialEndpoints = [
  '/',
  '/api',
  '/api/authentication_token',
  '/api/auth/google',
  '/api/auth/pbsmidata',
  '/api/auth/cevidb',
  '/api/auth/jubladb',
  '/api/auth/reset_password',
  '/api/auth/resend_activation',
  '/api/invitations',
  '/api/personal_invitations'
]

const metricsMap = new Map()

const loginTrend = new Trend('login')

for (const [_, value] of Object.entries(getUrlsToMeasure()._links)) {
  const urlWithoutTemplate = value.href.replace(/{.*$/, '')
  if (!specialEndpoints.includes(urlWithoutTemplate)) {
    metricsMap.set(`${urlWithoutTemplate}`, new Trend(toMetricName(urlWithoutTemplate)))
    metricsMap.set(`${urlWithoutTemplate}/item`, new Trend(toMetricName(`${urlWithoutTemplate}/item`)))
  }
}


export default function() {
  const payload = JSON.stringify({
    identifier: __ENV.USER || 'test@example.com', password: __ENV.PASSWORD || 'test',
  })
  const loginResponse = http.post(
    `${host}/api/authentication_token`,
    payload,
    { headers: { 'Content-Type': 'application/json' } })
  loginTrend.add(loginResponse.timings.duration);

  for (const [_, value] of Object.entries(getUrlsToMeasure()._links)) {
    const urlWithoutTemplate = value.href.replace(/{.*$/, '')
    if (!specialEndpoints.includes(urlWithoutTemplate)) {
      const url = `${host}${urlWithoutTemplate}.jsonhal`
      const collection = http.get(url)

      metricsMap.get(urlWithoutTemplate)?.add(collection.timings.duration)

      try {
        const itemUrl = JSON.parse(collection.body)._embedded?.items[0]._links.self.href
        const itemResponse = http.get(`${host}${itemUrl}.jsonhal`)
        metricsMap.get(`${urlWithoutTemplate}/item`)?.add(itemResponse.timings.duration)
      }
      // this also catches the ReferenceError on the long path to find the item url
      catch (e) 
      {
        console.log(`Failed to get item for url ${url}, reason: `, e)
        metricsMap.get(`${urlWithoutTemplate}/item`)?.add(-1E9)
      }
    }
  }
}


// noinspection JSUnusedGlobalSymbols
export function handleSummary(data) {
  return {
    'stdout': JSON.stringify(data),
  };
}


function getUrlsToMeasure() {
  return {
    '_links': {
      'self': {
        'href': '/api/',
      },
      'invitations': {
        'href': '/api/invitations{/id}{/action}',
        'templated': true,
      },
      'personalInvitations': {
        'href': '/api/personal_invitations{/id}{/action}',
        'templated': true,
      },
      'activities': {
        'href': '/api/activities{/id}{?camp,camp[]}',
        'templated': true,
      },
      'activityProgressLabels': {
        'href': '/api/activity_progress_labels{/id}{?camp,camp[]}',
        'templated': true,
      },
      'activityResponsibles': {
        'href': '/api/activity_responsibles{/id}{?activity,activity[],activity.camp,activity.camp[]}',
        'templated': true,
      },
      'camps': {
        'href': '/api/camps{/id}{?isPrototype,isPrototype[]}',
        'templated': true,
      },
      'campCollaborations': {
        'href': '/api/camp_collaborations{/id}{/action}{?camp,camp[],activityResponsibles.activity,activityResponsibles.activity[]}',
        'templated': true,
      },
      'categories': {
        'href': '/api/categories{/id}{?camp,camp[]}',
        'templated': true,
      },
      'checklists': {
        'href': '/api/checklists{/id}{?camp,camp[],isPrototype,isPrototype[]}',
        'templated': true,
      },
      'checklistItems': {
        'href': '/api/checklist_items{/id}{?checklist,checklist[],checklist.camp,checklist.camp[]}',
        'templated': true,
      },
      'contentNodes': {
        'href': '/api/content_nodes{?contentType,contentType[],root,root[],period}',
        'templated': true,
      },
      'checklistNodes': {
        'href': '/api/content_node/checklist_nodes{/id}{?contentType,contentType[],root,root[],period}',
        'templated': true,
      },
      'columnLayouts': {
        'href': '/api/content_node/column_layouts{/id}{?contentType,contentType[],root,root[],period}',
        'templated': true,
      },
      'materialNodes': {
        'href': '/api/content_node/material_nodes{/id}{?contentType,contentType[],root,root[],period}',
        'templated': true,
      },
      'multiSelects': {
        'href': '/api/content_node/multi_selects{/id}{?contentType,contentType[],root,root[],period}',
        'templated': true,
      },
      'responsiveLayouts': {
        'href': '/api/content_node/responsive_layouts{/id}{?contentType,contentType[],root,root[],period}',
        'templated': true,
      },
      'singleTexts': {
        'href': '/api/content_node/single_texts{/id}{?contentType,contentType[],root,root[],period}',
        'templated': true,
      },
      'storyboards': {
        'href': '/api/content_node/storyboards{/id}{?contentType,contentType[],root,root[],period}',
        'templated': true,
      },
      'contentTypes': {
        'href': '/api/content_types{/id}{?categories,categories[]}',
        'templated': true,
      },
      'days': {
        'href': '/api/days{/id}{?period,period[],period.camp,period.camp[]}',
        'templated': true,
      },
      'dayResponsibles': {
        'href': '/api/day_responsibles{/id}{?day,day[],day.period,day.period[]}',
        'templated': true,
      },
      'materialItems': {
        'href': '/api/material_items{/id}{?materialList,materialList[],materialNode,materialNode[],period}',
        'templated': true,
      },
      'materialLists': {
        'href': '/api/material_lists{/id}{?camp,camp[]}',
        'templated': true,
      },
      'periods': {
        'href': '/api/periods{/id}{?camp,camp[]}',
        'templated': true,
      },
      'profiles': {
        'href': '/api/profiles{/id}{?user.collaborations.camp,user.collaborations.camp[]}',
        'templated': true,
      },
      'scheduleEntries': {
        'href': '/api/schedule_entries{/id}{?period,period[],activity,activity[],start[before],start[strictly_before],start[after],start[strictly_after],end[before],end[strictly_before],end[after],end[strictly_after]}',
        'templated': true,
      },
      'users': {
        'href': '/api/users{/id}{/action}',
        'templated': true,
      },
      'login': {
        'href': '/api/authentication_token',
      },
      'oauthGoogle': {
        'href': '/api/auth/google{?callback}',
        'templated': true,
      },
      'oauthPbsmidata': {
        'href': '/api/auth/pbsmidata{?callback}',
        'templated': true,
      },
      'oauthCevidb': {
        'href': '/api/auth/cevidb{?callback}',
        'templated': true,
      },
      'oauthJubladb': {
        'href': '/api/auth/jubladb{?callback}',
        'templated': true,
      },
      'resetPassword': {
        'href': '/api/auth/reset_password{/id}',
        'templated': true,
      },
      'resendActivation': {
        'href': '/api/auth/resend_activation',
        'templated': false,
      },
    },
  }
}

function toMetricName(name) {
  return name.replaceAll("/", "_")
}
