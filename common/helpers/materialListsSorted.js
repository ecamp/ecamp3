import { sortBy } from 'lodash'

export default function (materialLists) {
  return sortBy(
    materialLists,
    (list) =>
      (list.campCollaboration == null ? 'NonUserList_' : 'UserList_') +
      list.name.toLowerCase()
  )
}
