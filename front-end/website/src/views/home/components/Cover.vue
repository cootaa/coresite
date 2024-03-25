<template>
  <a-typography class="home-cover">
    <a-space align="center" direction="vertical" fill>
      <div class="home-title">
        {{ $t('home.content.title') }}
      </div>
      <div class="home-summary">
        {{ $t('home.content.description') }}
      </div>
    </a-space>
  </a-typography>
</template>

<style scoped>
.home-cover {
  width: 90vw;
}
.home-title {
  font-family:
    'Mona Sans',
    'Mona Sans Header Fallback',
    -apple-system,
    BlinkMacSystemFont,
    'Segoe UI',
    Helvetica,
    Arial,
    sans-serif,
    'Apple Color Emoji',
    'Segoe UI Emoji',
    'Segoe UI Symbol';
  font-size: 7vw;
  font-weight: 600 !important;
}
.home-summary {
  width: 70vw;
  font-size: 2.2vw;
  color: var(--color-neutral-6);
}
</style>
