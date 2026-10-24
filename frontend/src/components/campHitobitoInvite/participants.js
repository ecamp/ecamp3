/**
 * Helpers for matching Hitobito event participants against the collaborators of an eCamp camp.
 */

export function normalizeEmail(email) {
  return (email ?? '').trim().toLowerCase()
}

/**
 * Splits the participants of a Hitobito event into the ones which have to be invited, and the
 * ones which are already part of the camp.
 *
 * @param participants {Array<{firstName: ?string, lastName: ?string, nickname: ?string, email: string}>}
 * @param campEmails {string[]} email addresses of the people which are already part of the camp
 */
export function partitionParticipants(participants, campEmails) {
  const knownEmails = new Set(campEmails.map(normalizeEmail))

  return {
    newParticipants: participants.filter(
      (participant) => !knownEmails.has(normalizeEmail(participant.email))
    ),
    existingParticipants: participants.filter((participant) =>
      knownEmails.has(normalizeEmail(participant.email))
    ),
  }
}

/**
 * Name of a Hitobito event participant, in the form "<first name> <last name> (<nickname>)".
 */
export function participantDisplayName({ firstName, lastName, nickname, email }) {
  const name = [firstName, lastName].filter(Boolean).join(' ')
  if (name && nickname) {
    return `${name} (${nickname})`
  }
  return name || nickname || email
}
