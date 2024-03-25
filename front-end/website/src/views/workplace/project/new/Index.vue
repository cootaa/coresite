<template>
  <a-spin :loading="loading">
    <a-space class="create-project" direction="vertical" :size="12" fill>
      <!-- Select Icon -->
      <a-row align="center" :gutter="24">
        <a-col flex="none">
          <!-- Select Icon Dropdown -->
          <a-dropdown @select="handleSelectIcon">
            <!-- Selected Icon -->
            <a-button type="none" class="create-project-icon arco-upload-picture-card">
              <div class="arco-upload-picture-card-text">
                <component :is="'icon-' + selectedIcon" v-if="selectedIcon" />
                <div class="create-project-icon-text" v-else>icon</div>
              </div>
            </a-button>
            <!-- Icon List -->
            <template #content>
              <a-doption v-for="item in ProjectIcons" :value="item.icon" :key="item.icon">
                <template #icon>
                  <component :is="'icon-' + item.icon" />
                </template>
                <template #default>{{ item.text }}</template>
              </a-doption>
            </template>
          </a-dropdown>
        </a-col>
        <!-- Select Icon PS -->
        <a-col flex="auto">
          {{ $t('workplace.project.create.icon.ps') }}
        </a-col>
      </a-row>
      <!-- Project Name -->
      <a-input v-model="name" :placeholder="$t('workplace.project.create.name.placeholder')" />
      <!-- Project Create Submit -->
      <a-button type="primary" @click="handleSubmit">{{
        $t('workplace.project.create.submit')
      }}</a-button>
    </a-space>
  </a-spin>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'

import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

import useLoading from '@/hooks/loading'
import useGroupStore from '@/stores/group'

import ProjectIcons from '@/config/projectIcons'
import { Message } from '@arco-design/web-vue'
import type { ICreateProjectResponse } from '../api'
import type { HttpResponse } from '@/utils/interceptor'

const route = useRoute()
const groupStore = useGroupStore()

const { t } = useI18n()
const { loading, setLoading } = useLoading()

// 当前组织 ID
const group_id = computed(() => Number(route.params.group_id))
// 项目图标
const selectedIcon = ref<String>()
// 项目名称
const name = ref<String>()

/**
 * 选择图标
 */
const handleSelectIcon = (icon: string) => {
  selectedIcon.value = icon
}

/**
 * 创建项目
 */
const handleSubmit = () => {
  // 验证表单信息
  if (!selectedIcon.value) return Message.error(t('workplace.project.create.no.icon'))
  if (!name.value) return Message.error(t('workplace.project.create.no.name'))
  setLoading(true)
  groupStore
    .createProject({
      group_id: group_id.value,
      name: name.value as string,
      icon: selectedIcon.value as string
    })
    .then((res) => {
      const { msg } = res as unknown as HttpResponse<ICreateProjectResponse>
      Message.success(msg)
    })
    .finally(() => {
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    })
}
</script>

<style lang="less" scoped>
@import '@/assets/style/global.less';

.create-project {
  &-icon {
    font-size: 48px;
    line-height: 1;

    &-text {
      font-size: 12px;
    }
  }
}
</style>
