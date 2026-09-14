<script setup>
import { ref } from 'vue'

const props = defineProps({ modelValue: { type: File, default: null } })
const emit = defineEmits(['update:modelValue'])

const isDragging = ref(false)
const inputRef = ref(null)

function handleDrop(e) {
  isDragging.value = false
  const file = e.dataTransfer.files?.[0]
  if (file) emit('update:modelValue', file)
}

function handleChange(e) {
  const file = e.target.files?.[0]
  if (file) emit('update:modelValue', file)
}

function clear() {
  emit('update:modelValue', null)
  if (inputRef.value) inputRef.value.value = ''
}
</script>

<template>
  <div
    class="rounded-sheet border-2 border-dashed px-6 py-10 text-center transition"
    :class="isDragging ? 'border-teal bg-teal/5' : 'border-border bg-surface'"
    @dragover.prevent="isDragging = true"
    @dragleave.prevent="isDragging = false"
    @drop.prevent="handleDrop"
  >
    <template v-if="!modelValue">
      <svg class="mx-auto h-10 w-10 text-muted" viewBox="0 0 24 24" fill="none">
        <path d="M12 16V4m0 0 4 4m-4-4-4 4M5 16v2a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
      <p class="mt-3 text-sm font-medium text-ink/90">Arrastra tu PDF o Word aquí</p>
      <p class="mt-1 text-xs text-muted">o</p>
      <label class="btn-ghost mt-3 inline-flex cursor-pointer text-sm">
        Selecciona un archivo
        <input ref="inputRef" type="file" accept=".pdf,.doc,.docx" class="hidden" @change="handleChange" />
      </label>
    </template>

    <template v-else>
      <div class="mx-auto flex max-w-sm items-center gap-3 rounded-lg border border-border bg-elevated px-4 py-3 text-left">
        <svg class="h-6 w-6 shrink-0 text-amber" viewBox="0 0 24 24" fill="none">
          <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
        </svg>
        <div class="min-w-0 flex-1">
          <p class="truncate text-sm font-medium text-ink/90">{{ modelValue.name }}</p>
          <p class="text-xs text-muted">{{ (modelValue.size / 1024 / 1024).toFixed(1) }} MB</p>
        </div>
        <button type="button" class="text-muted hover:text-red-400" @click="clear">✕</button>
      </div>
    </template>
  </div>
</template>