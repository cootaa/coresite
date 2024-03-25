import { computed, reactive } from 'vue'

/**
 * 路径对象
 */
export interface IPathItem {
  folder_id: number // 文件夹 ID
  name: string // 文件名
}

export default function usePath() {
  const path = reactive<IPathItem[]>([])

  // 当前路径
  const currentPath = computed(() => path[path.length - 1])

  // 添加路径
  const addPath = (item: IPathItem) => {
    path.push(item)
  }

  // 上一级
  const prevPath = () => {
    path.pop()
  }

  // 去到某个路径
  const goPath = (item: IPathItem) => {
    path.splice(path.indexOf(item))
  }

  return {
    path,
    currentPath,
    addPath,
    prevPath,
    goPath
  }
}
