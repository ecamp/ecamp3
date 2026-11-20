/**
 * Parses a page selection such as "1-3, 5" into zero-based page indices.
 * An empty selection means all pages.
 */
export function parsePageRange(pageRange, pageCount) {
  const allPages = Array.from({ length: pageCount }, (_, index) => index)
  if (!pageRange?.trim()) return allPages

  return pageRange.split(',').flatMap((part) => {
    const [first, last = first] = part.split('-').map((number) => parseInt(number, 10))
    if (Number.isNaN(first) || Number.isNaN(last)) return []
    return allPages.filter((index) => index >= first - 1 && index <= last - 1)
  })
}
