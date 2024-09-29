import type {
  CollectionReference,
  ResourceInterface,
  ResourceReference,
  HalJsonVuex,
} from 'hal-json-vuex'

interface UserEntity extends ResourceInterface<UserEntity> {
  id: string
  displayName: string

  profile: ResourceReference<ProfileEntity>
}

interface ProfileEntity extends ResourceInterface<ProfileEntity> {
  id: string
  firstname: string
  surname: string
  nickname: string
  legalName: string

  email: string

  language: string

  user: ResourceReference<UserEntity>
}

interface CampEntity extends ResourceInterface<CampEntity> {
  id: string
  name: string
  title: string
  motto: string

  isPrototype: boolean
  creator: ResourceReference<UserEntity>

  addressName: string | null
  addressStreet: string | null
  addressZipcode: string | null
  addressCity: string | null

  coachName: string | null
  courseKind: string | null
  courseNumber: string | null
  organizer: string | null
  kind: string | null
  printYSLogoOnPicasso: boolean | null
  trainingAdvisorName: string | null

  activities: CollectionReference<ActivityEntity>
  periods: CollectionReference<PeriodEntity>
  categories: CollectionReference<CategoryEntity>
  profiles: CollectionReference<ProfileEntity>
  progressLabels: CollectionReference<ActivityProgressLabelEntity>
}

interface PeriodEntity extends ResourceInterface<PeriodEntity> {
  id: string
  start: string
  end: string

  camp: ResourceReference<CampEntity>
}

interface ActivityEntity extends ResourceInterface<ActivityEntity> {
  id: string
  title: string
  location: string

  camp: ResourceReference<CampEntity>
  category: ResourceReference<CategoryEntity>
  period: ResourceReference<PeriodEntity>
  scheduleEntries: CollectionReference<ScheduleEntryEntity>
  activityResponsibles: CollectionReference<ActivityResponsibleEntity>
  activityProgressLabel: ResourceReference<ActivityProgressLabelEntity>
}

interface ActivityProgressLabelEntity
  extends ResourceInterface<ActivityProgressLabelEntity> {
  id: string
  title: string
  position: number
  camp: ResourceReference<CampEntity>
}

interface ActivityResponsibleEntity extends ResourceInterface<ActivityResponsibleEntity> {
  id: string
  activity: ResourceReference<ActivityEntity>
  campCollaboration: ResourceReference<CampCollaborationEntity>
}

interface ScheduleEntryEntity extends ResourceInterface<ScheduleEntryEntity> {
  id: string
  start: string
  end: string

  dayNumber: number
  scheduleEntryNumber: number
  number: string

  left: number
  width: number

  period: ResourceReference<PeriodEntity>
  day: ResourceReference<DayEntity>
}

interface DayEntity extends ResourceInterface<DayEntity> {
  id: string
  start: string
  end: string

  number: number
  dayOffset: number

  period: ResourceReference<PeriodEntity>
  scheduleEntries: CollectionReference<ScheduleEntryEntity>
  dayResponsibles: CollectionReference<DayResponsibleEntity>
}

interface DayResponsibleEntity extends ResourceInterface<DayResponsibleEntity> {
  id: string
  day: ResourceReference<DayEntity>
  campCollaboration: ResourceReference<CampCollaborationEntity>
}

interface CategoryEntity extends ResourceInterface<CategoryEntity> {
  id: string
  short: string
  name: string

  numberingStyle: 'a' | 'A' | 'i' | 'I' | '1'
  color: string

  camp: ResourceReference<CampEntity>
  contentNodes: CollectionReference<ContentNode>
  rootContentNode: ResourceReference<ContentNode>
  preferredContentNodes: CollectionReference<ContentNode>
}

type ContentNode =
  | ColumnLayoutNodeEntity
  | MultiSelectNodeEntity
  | SingleTextNodeEntity
  | StoryboardNodeEntity

interface ContentNodesBase<Data = unknown> {
  id: string
  contentTypeName: string
  instanceName: string | null
  slot: string
  position: number
  data: Data

  contentType: ResourceReference<ContentTypeEntity>

  children: CollectionReference<ContentNode>
  parent: ResourceReference<ContentNode>
  root: ResourceReference<ContentNode>
}

interface ColumnLayoutNodeData {
  columns: {
    slot: string
    width: number
  }[]
}

interface ColumnLayoutNodeEntity
  extends ContentNodesBase<ColumnLayoutNodeData>,
    ResourceInterface<ColumnLayoutNodeEntity> {}

interface MultiSelectNodeData {
  options: {
    [key: string]: {
      checked: boolean
    }
  }
}

interface MultiSelectNodeEntity
  extends ContentNodesBase<MultiSelectNodeData>,
    ResourceInterface<MultiSelectNodeEntity> {}

interface SingleTextNodeData {
  html: string
}

interface SingleTextNodeEntity
  extends ContentNodesBase<SingleTextNodeData>,
    ResourceInterface<SingleTextNodeEntity> {}

interface StoryboardNodeData {
  sections: {
    [key: string]: {
      column1: string
      column2Html: string
      column3: string
      position: number
    }
  }
}

interface StoryboardNodeEntity
  extends ContentNodesBase<StoryboardNodeData>,
    ResourceInterface<StoryboardNodeEntity> {}

interface MaterialNodeEntity
  extends ContentNodesBase<null>,
    ResourceInterface<MaterialNodeEntity> {
  materialItems: CollectionReference<MaterialItemEntity>
}

interface ContentTypeEntity extends ResourceInterface<ContentTypeEntity> {
  id: string
  name: string

  contentNodes: CollectionReference<ContentNode>
}

interface CampCollaborationEntity extends ResourceInterface<CampCollaborationEntity> {
  id: string
  role: 'member' | 'manager' | 'guest'
  status: 'invited' | 'established' | 'inactive'
  camp: ResourceReference<CampEntity>

  inviteEmail: string | null
  user: ResourceReference<UserEntity> | null
}

interface MaterialListEntity extends ResourceInterface<MaterialListEntity> {
  id: string
  name: string

  itemCount: number
  camp: ResourceReference<CampEntity>
  campCollaboration: ResourceReference<CampCollaborationEntity>
}

interface MaterialItemBase {
  id: string
  article: string
  quantity: number
  unit: string
  materialList: ResourceReference<MaterialListEntity>
}

type MaterialItemEntity = MaterialItemNodeEntity | MaterialItemPeriodEntity

interface MaterialItemNodeEntity
  extends MaterialItemBase,
    ResourceInterface<MaterialItemNodeEntity> {
  materialNode: ResourceReference<MaterialNodeEntity>
  period: null
}

interface MaterialItemPeriodEntity
  extends MaterialItemBase,
    ResourceInterface<MaterialItemPeriodEntity> {
  materialNode: null
  period: ResourceReference<PeriodEntity>
}

interface InvitationDTO extends ResourceInterface<InvitationDTO> {
  campId: string
  campTitle: string
  userDisplayName: string | null
  userAlreadyInCamp: boolean | null
}

interface InvitationDTOParams {
  action: 'find' | 'accept' | 'reject'
  id: string
}

type CampParam = { camp?: string | string[] }
type ActivityParam = { activity?: string | string[] }
type PeriodParam = { period?: string | string[] }
type ActivityResponsiblesParams = ActivityParam & {
  activity?: { camp: string | string[] }
}
type ActivityResponsibleParams = {
  activityResponsibles?: ActivityParam
}
type CampPrototypeQueryParam = { isPrototype: boolean }
type ContentNodeParam = {
  contentType?: string | string[]
  root?: string | string[]
  period?: string
}
type CategoryParam = { categories?: string | string[] }
type DayParam = { day?: string | string[] }
type DayResponsibleParams = DayParam & {
  day?: { period: string | string[] }
}
type MaterialListParam = { materialList?: string | string[] }
type MaterialNodeParam = { materialNode?: string | string[] }
type MaterialItemParams = MaterialListParam & MaterialNodeParam & { period?: string }
type ProfileParams = {
  user: { collaborations: { camp: string | string[] } }
}
type TimeParam = {
  before?: string
  strictly_before?: string
  after?: string
  strictly_after?: string
}
type ScheduleEntryParams = PeriodParam &
  ActivityParam & {
  start?: TimeParam
  end?: TimeParam
}

type SingleResource<T extends ResourceInterface<T>> = ResourceReference<T, { id: string }>
type QueryResources<
  T extends ResourceInterface<T>,
  Params = undefined,
> = CollectionReference<T, Params>

export interface RootEndpoint extends ResourceInterface<RootEndpoint> {
  activities: QueryResources<ActivityEntity, CampParam> & SingleResource<ActivityEntity>

  activityProgressLabels: QueryResources<ActivityProgressLabelEntity, CampParam> &
    SingleResource<ActivityProgressLabelEntity>

  activityResponsibles: QueryResources<
    ActivityResponsibleEntity,
    ActivityResponsiblesParams
  > &
    SingleResource<ActivityResponsibleEntity>

  campCollaborations: QueryResources<
    CampCollaborationEntity,
    CampParam & ActivityResponsibleParams
  > &
    SingleResource<CampCollaborationEntity>

  camps: QueryResources<CampEntity, CampPrototypeQueryParam> & SingleResource<CampEntity>

  categories: QueryResources<CategoryEntity, CampParam> & SingleResource<CategoryEntity>

  columnLayouts: QueryResources<ColumnLayoutNodeEntity, ContentNodeParam> &
    SingleResource<ColumnLayoutNodeEntity>

  contentNodes: CollectionReference<ContentNode, ContentNodeParam>

  contentTypes: QueryResources<ContentTypeEntity, CategoryParam> &
    SingleResource<ContentTypeEntity>

  days: CollectionReference<DayEntity> | SingleResource<DayEntity>

  dayResponsibles: QueryResources<DayResponsibleEntity, DayResponsibleParams> &
    SingleResource<DayResponsibleEntity>

  invitations: ResourceReference<InvitationDTO, InvitationDTOParams>

  login: ResourceReference<never>

  materialItems: QueryResources<MaterialItemEntity, MaterialItemParams> &
    SingleResource<MaterialItemEntity>

  materialLists: QueryResources<MaterialListEntity, CampParam> &
    SingleResource<MaterialListEntity>

  materialNodes: QueryResources<MaterialNodeEntity, ContentNodeParam> &
    SingleResource<MaterialNodeEntity>

  multiSelects: QueryResources<MultiSelectNodeEntity, ContentNodeParam> &
    SingleResource<MultiSelectNodeEntity>

  oauthCevidb: ResourceReference<never>

  oauthGoogle: ResourceReference<never>

  oauthJubladb: ResourceReference<never>

  oauthPbsmidata: ResourceReference<never>

  periods: QueryResources<PeriodEntity, CampParam> & SingleResource<PeriodEntity>

  profiles: QueryResources<ProfileEntity, ProfileParams> & SingleResource<ProfileEntity>

  resetPassword: ResourceReference<never>

  scheduleEntries: QueryResources<ScheduleEntryEntity, ScheduleEntryParams> &
    SingleResource<ScheduleEntryEntity>

  singleTexts: QueryResources<SingleTextNodeEntity, ContentNodeParam> &
    SingleResource<SingleTextNodeEntity>

  storyboards: QueryResources<StoryboardNodeEntity, ContentNodeParam> &
    SingleResource<StoryboardNodeEntity>

  users: CollectionReference<UserEntity> & SingleResource<UserEntity>
}

declare module 'vue/types/vue' {
  interface Vue {
    api: HalJsonVuex<RootEndpoint>
  }
}
