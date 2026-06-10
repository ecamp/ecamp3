import { test } from '@playwright/test'
import { loginAndSetCookie, expectCachePass } from '@/utils/helpers'

test("doesn't cache /content_node/checklist_nodes/{checklistNodeId}/checklist_items", async ({
  page,
  request,
}) => {
  // TODO: ensure logic to purge this route is propertly implemented before changing this test
  // https://github.com/ecamp/ecamp3/pull/9849/changes#r3330858355

  const checklistNodeId = '42a32ff460d3'
  const uri = `/api/content_node/checklist_nodes/${checklistNodeId}/checklist_items`
  await loginAndSetCookie(page, request, 'test@example.com')
  await expectCachePass(request, uri)
})
