<template>
  <a-spin :loading="loading">
    <a-space class="update-project" direction="vertical" :size="12" fill>
      <!-- Select Icon -->
      <a-row align="center" :gutter="24">
        <a-col flex="none">
          <!-- Select Icon Dropdown -->
          <a-dropdown @select="handleSelectIcon">
            <!-- Selected Icon -->
            <a-button type="none" class="update-project-icon arco-upload-picture-card">
              <div class="arco-upload-picture-card-text">
                <component :is="'icon-' + selectedIcon" v-if="selectedIcon" />
                <div class="update-project-icon-text" v-else>icon</div>
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
          {{ $t('workplace.project.setting.update.icon.ps') }}
        </a-col>
      </a-row>
      <!-- Project Name -->
      <a-input
        v-model="name"
        :placeholder="$t('workplace.project.setting.update.name.placeholder')"
      />
      <!-- Project Update Submit -->
      <a-button type="primary" @click="handleSubmit">{{
        $t('workplace.project.setting.update.submit')
      }}</a-button>
    </a-space>
  </a-spin>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'

import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

import useLoading from '@/hooks/loading'
import useGroupStore from '@/stores/group'

import ProjectIcons from '@/config/projectIcons'
import { Message } from '@arco-design/web-vue'
import type { IProjectItem } from '../api'

const props = defineProps<{
  project?: IProjectItem
}>()

const route = useRoute()
const groupStore = useGroupStore()

const { t } = useI18n()
const { loading, setLoading } = useLoading()

// 当前组织 ID
const group_id = computed(() => Number(route.params.group_id))
// 项目图标
const selectedIcon = ref<string>()
// 项目名称
const name = ref<string>()

watch(
  () => props.project,
  (val) => {
    selectedIcon.value = val?.project_info.icon
    name.value = val?.project_info.name
  }
)

/**
 * 选择图标
 */
const handleSelectIcon = (icon: string) => {
  selectedIcon.value = icon
}

/**
 * 更新项目
 */
const handleSubmit = () => {
  setLoading(true)
  groupStore
    .updateProjectData({
      group_id: group_id.value,
      project_id: props.project?.project_id as number,
      name: name.value,
      icon: selectedIcon.value
    })
    .then(() => {
      Message.success(t('workplace.project.setting.update.success'))
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

.update-project {
  &-icon {
    font-size: 48px;
    line-height: 1;

    &-text {
      font-size: 12px;
    }
  }
}
</style>
