import { isEmptyHtml } from '../../helpers.js'

export function checklistItemsFor(contentNode, camp) {
  return camp
    .checklists()
    .items.flatMap((checklist) => checklist.checklistItems().items)
    .filter((item) =>
      item
        .checklistNodes()
        .items.some(
          (checklistNode) => checklistNode._meta.self === contentNode._meta.self
        )
    )
}

export function storyboardSectionHasContent(section) {
  return (
    section.column1.length > 0 ||
    !isEmptyHtml(section.column2Html) ||
    section.column3.length > 0
  )
}

const emptinessChecks = {
  LAThematicArea: (contentNode) =>
    !Object.values(contentNode.data.options).some((option) => option.checked),
  LearningObjectives: (contentNode) => isEmptyHtml(contentNode.data.html),
  LearningTopics: (contentNode) => isEmptyHtml(contentNode.data.html),
  Notes: (contentNode) => isEmptyHtml(contentNode.data.html),
  SafetyConsiderations: (contentNode) => isEmptyHtml(contentNode.data.html),
  Storycontext: (contentNode) => isEmptyHtml(contentNode.data.html),
  Storyboard: (contentNode) =>
    !Object.values(contentNode.data.sections).some(storyboardSectionHasContent),
  Material: (contentNode) => contentNode.materialItems().items.length === 0,
  Checklist: (contentNode, camp) => checklistItemsFor(contentNode, camp).length === 0,
}

export function isEmptyContentNode(contentNode, camp) {
  const isEmpty = emptinessChecks[contentNode.contentTypeName]
  return isEmpty ? isEmpty(contentNode, camp) : false
}
