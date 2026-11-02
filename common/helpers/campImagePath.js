export function campImagePath(src) {
  return /^\/(?:api\/)?(camps\/[0-9a-f]{12,16}\/images\/[0-9a-f]{12,16})$/.exec(
    src ?? ''
  )?.[1]
}
