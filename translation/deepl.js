'use string'
const fs = require('fs')
const path = require('path')
const deepl = require('deepl-node')

const args = process.argv.slice(2)
if (args.length != 2) {
  console.warn(
    ' > use this script with source and target translation-file\r\n' +
      ' > node ' +
      process.argv[1] +
      ' ../frontend/src/common/locales/en.json ../frontend/src/common/locales/fr.json'
  )
  process.exit()
}

const translator = new deepl.Translator('deepl-api-key')

const source = args[0]
if (!fs.existsSync(source)) {
  console.warn(' > source-file does not exists')
  process.exit()
}
const target = args[1]
if (!fs.existsSync(target)) {
  console.warn(' > target-file does not exists')
  process.exit()
}

const sourceLang = path.basename(source).substring(0, 2)
let targetLang = path.basename(target).substring(0, 2)

if (targetLang == 'en') {
  targetLang = 'en-GB'
}

;(async () => {
  let sourceFileContent = fs.readFileSync(source)
  let sourceJson = JSON.parse(sourceFileContent)

  let targetFileContent = fs.readFileSync(target)
  let targetJson = JSON.parse(targetFileContent)

  targetJson = await translateJson(sourceJson, targetJson, '')
  fs.writeFileSync(target, JSON.stringify(targetJson, null, 2))

  console.log('done')

  async function translateJson(sourceJson, targetJson, path) {
    for (const key in sourceJson) {
      let sourceVal = sourceJson[key]

      if (path == '.global.datetime') {
        // DateTime Formats must not be translated
        //
      } else if (typeof sourceVal == 'string') {
        if (key in targetJson && targetJson[key] != '') {
          // Translation already available
        } else if (key == 'icon') {
          // icon-name must not be translated
        } else {
          targetJson[key] = (
            await translator.translateText(sourceVal, sourceLang, targetLang, {formality: 'prefer_less'})
          ).text
        }
      } else {
        let targetVal = {}
        if (key in targetJson) {
          targetVal = targetJson[key]
        }
        targetJson[key] = await translateJson(sourceVal, targetVal, path + '.' + key)
      }
    }
    return sortObject(targetJson)
  }

  function sortObject(obj) {
    return Object.keys(obj)
      .sort()
      .reduce(function (result, key) {
        result[key] = obj[key]
        return result
      }, {})
  }
})()
