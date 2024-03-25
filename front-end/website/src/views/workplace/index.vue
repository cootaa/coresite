<template>
  <a-spin :loading="loading">
    <a-layout class="fill-window">
      <a-layout-header>
        <Header />
      </a-layout-header>
      <a-layout>
        <Sider />
        <Content />
      </a-layout>
    </a-layout>
  </a-spin>
</template>

<script lang="ts" setup>
import { onMounted } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import useLoading from '@/hooks/loading'

import useGroupStore from '@/stores/group'
import useUserStore from '@/stores/user'

import Header from './components/Header.vue'
import Sider from './components/Sider.vue'
import Content from './components/Content.vue'

const groupStore = useGroupStore()
const userStore = useUserStore()

const route = useRoute()
const router = useRouter()

const { loading, setLoading } = useLoading(true)

onMounted(() => {
  getGroupsAndProjects()
})

/**
 * 获取组织列表和项目列表
 */
const getGroupsAndProjects = () => {
  const { user_id } = userStore
  const group_id = Number(route.params.group_id)
  // 判断是否登录
  if (!user_id) return
  setLoading(true)
  // 获取组织列表 index 0
  const getGroups = groupStore.getGroups({ user_id })
  // 获取项目列表 index 1
  const getProjects = groupStore.getProjects({ user_id, group_id })
  // 捕获错误
  // Promise.all([getGroups, getProjects])
  //   .catch(() => {
  //     router.back()
  //   })
  //   .finally(() => {
  //     setLoading(false)
  //   })
  Promise.allSettled([getGroups, getProjects])
    .then((res) => {
      // 判断错误类型
      const index = res.findIndex((item) => item.status === 'rejected')
      // 0 的错误是获取组织列表的错误，需要抛出错误返回上一页
      if (index === 0) {
        throw new Error()
      }
    })
    .catch(() => {
      router.back()
    })
    .finally(() => {
      setLoading(false)
    })
}
</script>

<script lang="ts">
export default {
  name: 'Workplace'
}
</script>
