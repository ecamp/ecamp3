import * as XLSX from 'xlsx'
import i18n from '@/plugins/i18n'

/**
 * @param camp
 * @param {Function} camp
 * @param {boolean} includeList
 * @returns {{downloadXlsx: ((function(): Promise<void>)|*)}}
 */
export function useDownloadMaterialList(camp, includeList = true) {
  const getActivity = async (materialItem) => {
    if (!materialItem.materialNode) {
      return null
    }
    const root = await materialItem.materialNode().$href('root')
    return camp()
      .activities()
      .items.find((activity) => activity.rootContentNode()._meta.self === root)
  }

  /**
   * @param {object} materialItemsPerPeriod
   * @param {object} materialItemsPerPeriod.period
   * @param {array} materialItemsPerPeriod.items
   * @param {string} name filename without extension
   * @returns {Promise<void>}
   */
  const downloadMaterialList = async (materialItemsPerPeriod, name) => {
    await camp().activities().$loadItems()
    let workbook = XLSX.utils.book_new()
    let sheets = await Promise.all(
      materialItemsPerPeriod.map(async ({ period, items }) => {
        let rows = await Promise.all(
          items.map(async (materialItem) => {
            let activity = await getActivity(materialItem)
            const scheduleEntries = activity
              ?.scheduleEntries()
              .items.map((item) => item.number)
              .join(', ')
            return [
              materialItem.quantity,
              materialItem.unit,
              materialItem.article,
              ...(includeList ? [materialItem.materialList().name] : []),
              activity?.title
                ? `${activity.category().short} ${activity?.title}: ${scheduleEntries}`
                : materialItem.period().description,
            ]
          })
        )
        let data = [
          [
            i18n.tc('entity.materialItem.fields.quantity'),
            i18n.tc('entity.materialItem.fields.unit'),
            i18n.tc('entity.materialItem.fields.article'),
            ...(includeList ? [i18n.tc('entity.materialItem.fields.list')] : []),
            i18n.tc('entity.materialItem.fields.reference'),
          ],
        ]
        rows.forEach((r) => data.push(r))
        return { description: period.description, data }
      })
    )
    sheets.forEach((s) => {
      let worksheet = XLSX.utils.aoa_to_sheet(s.data)
      let validSheetName = s.description.replaceAll(/[?*[\]/\\:]/g, '')
      workbook.SheetNames.push(validSheetName)
      workbook.Sheets[validSheetName] = worksheet
    })
    XLSX.writeFile(workbook, name + '.xlsx')
  }

  return {
    downloadMaterialList,
  }
}
