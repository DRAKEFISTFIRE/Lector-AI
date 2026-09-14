<script setup>
defineProps({
  eyebrow: { type: String, default: 'Lector AI' },
  title: { type: String, required: true },
  copy: { type: String, required: true },
})
</script>

<template>
  <div class="relative hidden overflow-hidden bg-surface px-12 py-14 lg:flex lg:w-1/2 lg:flex-col lg:justify-between">
    <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-teal/10 blur-3xl"></div>
    <div class="absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-amber/10 blur-3xl"></div>

    <div class="relative z-10">
      <span class="font-display text-lg font-medium">{{ eyebrow }}</span>
    </div>

    <div class="relative z-10 max-w-md">
      <h1 class="font-display text-4xl font-medium leading-tight text-ink">{{ title }}</h1>
      <p class="mt-4 text-muted">{{ copy }}</p>
    </div>

    <div class="relative z-10 mt-10 flex items-center gap-4">
      <div class="animate-[fadein_0.8s_ease-out] rounded-lg border border-border bg-canvas p-4 shadow-lg">
        <div class="mb-2 h-2 w-16 rounded bg-ink/30"></div>
        <div class="mb-1.5 h-1.5 w-24 rounded bg-ink/15"></div>
        <div class="mb-1.5 h-1.5 w-20 rounded bg-ink/15"></div>
        <div class="h-1.5 w-24 rounded bg-ink/15"></div>
      </div>
      <svg class="h-5 w-5 shrink-0 text-teal" viewBox="0 0 24 24" fill="none">
        <path d="M5 12h14m0 0-6-6m6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
      <div class="flex gap-2">
        <div class="h-16 w-11 rounded-md border border-amber/40 bg-elevated"></div>
        <div class="h-16 w-11 translate-y-2 rounded-md border border-teal/40 bg-elevated"></div>
        <div class="h-16 w-11 rounded-md border border-amber/40 bg-elevated"></div>
      </div>
    </div>
  </div>
</template>