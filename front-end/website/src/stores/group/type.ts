import type { IGroupItem } from '@/views/group/api'
import type { IProjectItem } from '@/views/workplace/project/api'

export interface IGroupStoreState {
  groups: IGroupItem[]
  projects: IProjectItem[]
}
