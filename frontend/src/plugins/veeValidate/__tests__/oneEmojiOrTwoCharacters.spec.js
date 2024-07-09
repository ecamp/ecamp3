import oneEmojiOrTwoCharacters from '../oneEmojiOrTwoCharacters.js'

const mockI18n = {
  $tc: (key) => key,
}

describe('oneEmojiOrTwoCharacters validation', () => {
  it.each([
    ['1', true],
    ['12', true],
    ['123', false],
    ['🧑🏼‍🔧', true],
    ['🧑🏼‍🔧😊', false],
    ['😊', true],
    ['😊😊', false],
    ['a😊', false],
    ['', true],
    ['😊😊😊😊', false],
  ])('validates %s as %s', (input, expected) => {
    // given
    const rule = oneEmojiOrTwoCharacters(mockI18n)

    // when
    const result = rule.validate(input)

    // then
    expect(result).toBe(expected)
  })
})
