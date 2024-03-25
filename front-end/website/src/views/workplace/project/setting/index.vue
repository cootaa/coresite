<template>
  <a-drawer class="project-setting" :width="260">
    <!-- Project Icon & Name -->
    <template #header>
      <a-space class="project-setting-title" direction="vertical" align="center" fill>
        <!-- Icon -->
        <div class="project-setting-title-icon" @click="handleSettingProjectInfo">
          <component :is="'icon-' + props.project?.project_info.icon" />
          <span class="editor-icon"><icon-edit /></span>
        </div>
        <!-- Update Project Info -->
        <Modal
          v-model:visible="updateInfoVisible"
          :title="$t('workplace.project.setting.update.info')"
        >
          <UpdateInfo :project="props.project" />
        </Modal>
        <!-- Name -->
        <div class="project-setting-title-name">{{ props.project?.project_info.name }}</div>
      </a-space>
    </template>
    <!-- Project Member List -->
    <a-scrollbar style="height: auto; overflow: auto">
      <a-space direction="vertical" fill>
        <ProjectMemberList
          :loading="loading"
          :group-id="props.groupId"
          :project-id="props.projectId"
          :project-members="members"
          @handle-delete-member="handleDeleteMember"
        />
      </a-space>
    </a-scrollbar>
    <!-- Footer -->
    <template #footer>
      <a-space direction="vertical" align="start" fill>
        <!-- Project User Setting -->
        <a-link @click="handleOpenGroupMemberList">{{
          $t('workplace.project.setting.userSetting.button')
        }}</a-link>
        <!-- Project Close -->
        <a-popconfirm :content="projectStatusPS" @ok="handleProjectCloseOrRestore">
          <a-link>{{ projectStatusButton }}</a-link>
        </a-popconfirm>
      </a-space>
    </template>
    <!-- Group Member List -->
    <GroupMemberList
      :group-members="groupMembers"
      :project-members="members"
      :popup-container="props.popContainer"
      :visible="groupMemberVisible"
      @handle-add-project-member="handleAddProjectMember"
      @cancel="handleHideGroupMemberList"
    />
  </a-drawer>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'

import type { IMemberType } from '@/views/group/api/setting'
import type { IProjectItem } from '../api'

import { useI18n } from 'vue-i18n'

import useLoading from '@/hooks/loading'
import useGroupStore from '@/stores/group'

import { Message } from '@arco-design/web-vue'

import ProjectMemberList from './ProjectMemberList.vue'
import GroupMemberList from './GroupMemberList.vue'
import Modal from '@/layout/Modal.vue'
import UpdateInfo from './UpdateInfo.vue'

const props = defineProps<{
  groupId: number
  projectId: number
  project?: IProjectItem
  popContainer: string
}>()

const emit = defineEmits(['onHandleUpdateMemberList'])

const groupStore = useGroupStore()

const { t } = useI18n()
const { loading, setLoading } = useLoading(true)

// 项目成员
const members = ref<IMemberType[]>([])
// 组织成员
const groupMembers = ref<IMemberType[]>([])
// 项目开启状态
const isProjectOpen = computed(() => props.project?.status === 1)
// 项目关闭开启文案
const projectStatusButton = computed(() => {
  return isProjectOpen.value
    ? t('workplace.project.setting.closeProject.button')
    : t('workplace.project.setting.restoreProject.button')
})
const projectStatusPS = computed(() => {
  return isProjectOpen.value
    ? t('workplace.project.setting.closeProject.ps')
    : t('workplace.project.setting.restoreProject.ps')
})
// 项目信息更新
const updateInfoVisible = ref(false)

onMounted(() => {
  getMembers()
  getGroupMembers()
})

/**
 * 获取项目成员
 */
const getMembers = () => {
  if (!props.groupId || !props.projectId) setLoading(true)
  groupStore
    .getProjectMembers({
      group_id: props.groupId,
      project_id: props.projectId
    })
    .then((res) => {
      members.value = res
    })
    .finally(() => {
      emit('onHandleUpdateMemberList', members.value)
      setLoading(false)
    })
}

/**
 * 获取组织成员列表
 *
 */
const getGroupMembers = () => {
  // 验证表单
  if (!props.groupId) return
  // 提交表单
  setLoading(true)
  groupStore
    .getMembers({ group_id: props.groupId })
    .then((res) => {
      groupMembers.value = res.group_list
    })
    .finally(() => {
      setLoading(false)
    })
}

/**
 * 移除成员
 */
const handleDeleteMember = (user_id: number) => {
  // 验证表单
  if (!user_id || !props.groupId || !props.projectId) {
    return Message.error(t('workplace.project.delete.form.error'))
  }
  // 提交表单
  setLoading(true)
  groupStore
    .deleteProjectMember({
      group_id: props.groupId,
      project_id: props.projectId,
      member_ids: [user_id]
    })
    .then((res) => {
      Message.success(res?.data as string)
    })
    .finally(() => {
      setLoading(false)
      getMembers()
    })
}

/**
 * 打开组织成员列表
 */
const groupMemberVisible = ref(false)
const handleOpenGroupMemberList = () => {
  groupMemberVisible.value = true
}
const handleHideGroupMemberList = () => {
  groupMemberVisible.value = false
}

/**
 * 添加项目成员
 */
const handleAddProjectMember = (member_id: number) => {
  // 验证表单
  if (!member_id || !props.groupId || !props.projectId) {
    return Message.error(t('workplace.project.delete.form.error'))
  }
  // 提交表单
  setLoading(true)
  groupStore
    .addProjectMember({
      project_id: props.projectId,
      group_id: props.groupId,
      member_id
    })
    .then((res) => {
      Message.success(res?.data as string)
    })
    .finally(() => {
      setLoading(false)
      getMembers()
    })
}

/**
 * 修改项目状态
 */
const handleProjectCloseOrRestore = () => {
  // 检查表单
  if (!props.projectId) return
  // 提交表单
  setLoading(true)
  groupStore
    .dismissProject({
      project_id: props.projectId
    })
    .then((res) => {
      Message.success(res?.data as string)
    })
    .finally(() => {
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    })
}

const handleSettingProjectInfo = () => {
  updateInfoVisible.value = true
}
</script>

<style lang="less">
.project-setting {
  .arco-drawer-header {
    height: auto;
  }
}

.project-setting-title {
  width: 100%;
  padding: 12px;

  &-icon {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    width: 48px;
    height: 48px;
    line-height: 1;
    border-radius: 50%;
    background-color: var(--color-fill-2);
    cursor: pointer;

    .editor-icon {
      position: absolute;
      bottom: 0;
      right: 0;
      font-size: 16px;
      opacity: 0.5;
    }
  }

  &-name {
    font-weight: bold;
    font-size: 1rem;
  }
}
</style>
