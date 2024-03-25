<template>
  <a-modal modal-class="modal" :mask-closable="false" :footer="false">
    <slot></slot>
  </a-modal>
</template>

<style lang="less">
.modal {
  width: 90vw;
  max-width: 600px;
  min-width: 320px;
}
</style>
