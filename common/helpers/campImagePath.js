export function campImagePath(src) {
  return /^\/(?:api\/)?(images\/[0-9a-f]{64}\/content)$/.exec(src ?? '')?.[1]
}
