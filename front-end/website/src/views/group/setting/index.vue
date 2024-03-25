<template>
  <a-tabs position="left">
    <a-tab-pane key="1" :title="$t('group.setting.info.title')">
      <Info />
    </a-tab-pane>
    <a-tab-pane key="2" :title="$t('group.setting.userlist.title')">
      <UserList />
    </a-tab-pane>
    <a-tab-pane key="3" :title="$t('settings.otherSetting.title')">
      <Other />
    </a-tab-pane>
  </a-tabs>
</template>

<script lang="ts" setup>
import Info from './Info.vue'
import UserList from './UserList.vue'
import Other from './Other.vue'
</script>
