<template>
  <a-spin :loading="loading">
    <!-- Admin Action -->
    <a-row align="center" :gutter="12" :wrap="false">
      <a-col flex="auto">
        <a-input-group style="display: flex">
          <!-- Admin Type -->
          <a-select :options="adminOptions" :style="{ width: '10rem' }" v-model="adminHandle" />
          <!-- Admin Input -->
          <a-input v-model="adminHandleText" :placeholder="adminHandleTextPlaceholder" />
        </a-input-group>
      </a-col>
      <a-col flex="auto">
        <a-button class="width-100" type="primary" @click="handleAdminSubmit">{{
          $t('group.setting.user.options.submit')
        }}</a-button>
      </a-col>
    </a-row>
    <!-- User List -->
    <a-scrollbar style="min-height: 300px; max-height: 400px; height: 50vh; overflow: auto">
      <a-list :bordered="false">
        <a-list-item v-for="item in memberList" :key="item.user_id">
          <a-list-item-meta>
            <!-- User Nickname -->
            <template #title>{{ item.user.nickname }}</template>
            <!-- User Avatar -->
            <template #avatar>
              <a-image
                width="32"
                height="32"
                :src="item.user.avatar || UserDefaultAvatar"
                :preview="false"
              />
            </template>
          </a-list-item-meta>
          <!-- User Actions -->
          <template #actions>
            <a-popconfirm
              :content="$t('group.setting.user.delete.ps')"
              @ok="() => handleDeleteMember(item.group_id, item.user_id)"
            >
              <icon-delete />
            </a-popconfirm>
          </template>
        </a-list-item>
      </a-list>
    </a-scrollbar>
  </a-spin>
</template>

<script lang="ts">
export enum EAdminOption {
  Add = 'add',
  Search = 'search'
}
</script>

<script lang="ts" setup>
import { computed, onMounted, reactive, ref } from 'vue'

import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

import useLoading from '@/hooks/loading'
import useGroupStore from '@/stores/group'

import { UserDefaultAvatar } from '@/config/common'

import { Message, type SelectOptionData } from '@arco-design/web-vue'
import type { IMemberType } from '../api/setting'

const route = useRoute()
const groupStore = useGroupStore()

const { t } = useI18n()
const { loading, setLoading } = useLoading(true)

// 当前组织 ID
const group_id = computed(() => Number(route.params.group_id))
// 成员列表
const memberList = ref<IMemberType[]>([])
// 管理员操作选项
const adminOptions = reactive<SelectOptionData[]>([
  {
    label: t('group.setting.user.options.add'),
    value: EAdminOption.Add
  },
  {
    label: t('group.setting.user.options.search'),
    value: EAdminOption.Search
  }
])
// 管理员默认操作
const adminHandle = ref(EAdminOption.Add)
// 管理员操作文本框内容
const adminHandleText = ref('')
// 管理员操作文本框默认提示
const adminHandleTextPlaceholder = computed(() => {
  switch (adminHandle.value) {
    case EAdminOption.Add:
      return t('group.setting.user.options.add.text')
    case EAdminOption.Search:
      return t('group.setting.user.options.search.text')
    default:
      return ''
  }
})

onMounted(() => {
  getMembers()
})

/**
 * 获取成员列表
 *
 */
const getMembers = (nickname?: string) => {
  if (!group_id.value) return
  setLoading(true)
  groupStore
    .getMembers({ group_id: group_id.value, nickname })
    .then((res) => {
      memberList.value = res.group_list
    })
    .finally(() => {
      setLoading(false)
    })
}

/**
 * 管理员操作提交
 */
const handleAdminSubmit = () => {
  switch (adminHandle.value) {
    case EAdminOption.Add:
      handleAddMember()
      break
    case EAdminOption.Search:
      handleSearchMember()
      break
  }
}

/**
 * 添加成员
 */
const handleAddMember = () => {
  setLoading(true)
  groupStore
    .addMember({
      group_id: group_id.value,
      username: adminHandleText.value
    })
    .then((res) => {
      Message.success(res?.msg as string)
    })
    .finally(() => {
      setLoading(false)
      adminHandleText.value = ''
      // 重新获取成员列表
      getMembers()
    })
}

/**
 * 搜索成员
 */
const handleSearchMember = () => {
  getMembers(adminHandleText.value)
}

/**
 * 移除成员
 */
const handleDeleteMember = (group_id: number, user_id: number) => {
  setLoading(true)
  groupStore
    .deleteMember({
      group_id,
      member_ids: [user_id]
    })
    .then((res) => {
      // 移除成功
      Message.success(res?.data as string)
    })
    .finally(() => {
      setLoading(false)
      // 重新获取成员列表
      getMembers()
    })
}
</script>
