<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const messages = [
  'Leyendo tu documento…',
  'Detectando las ideas clave…',
  'Consultando a Gemini…',
  'Dando forma al contenido…',
  'Casi listo…',
]
const index = ref(0)
let interval

onMounted(() => {
  interval = setInterval(() => {
    index.value = (index.value + 1) % messages.length
  }, 1800)
})
onUnmounted(() => clearInterval(interval))
</script>

<template>
  <div class="flex flex-col items-center justify-center gap-4 py-16 text-center">
    <div class="relative h-14 w-14">
      <div class="absolute inset-0 animate-spin rounded-full border-2 border-border border-t-teal"></div>
      <div class="absolute inset-3 rounded-full bg-teal/10"></div>
    </div>
    <p class="font-display text-lg text-ink/90">{{ messages[index] }}</p>
    <p class="max-w-xs text-sm text-muted">Esto puede tardar unos segundos según el tamaño del documento.</p>
  </div>
</template>