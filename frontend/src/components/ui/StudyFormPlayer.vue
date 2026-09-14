<script setup>
import { ref } from 'vue'

defineProps({ form: { type: Object, required: true } })

const flipped = ref({})

function toggle(id) {
  flipped.value[id] = !flipped.value[id]
}
</script>

<template>
  <div class="flex flex-col gap-4">
    <p v-if="form.instructions" class="text-sm text-muted">{{ form.instructions }}</p>

    <div class="grid gap-4 sm:grid-cols-2">
      <button
        v-for="item in form.items"
        :key="item.id"
        type="button"
        class="card min-h-[9rem] p-5 text-left transition hover:border-teal/40"
        @click="toggle(item.id)"
      >
        <span class="mb-2 inline-block rounded-full bg-elevated px-2.5 py-0.5 text-[11px] font-medium text-muted">
          {{ item.type === 'flashcard' ? 'Flashcard' : 'Pregunta abierta' }}
        </span>

        <p v-if="!flipped[item.id]" class="font-medium text-ink">{{ item.prompt }}</p>
        <p v-else class="text-sm text-teal">{{ item.expected_answer || 'Sin respuesta modelo.' }}</p>

        <p class="mt-3 text-[11px] text-muted">Toca para {{ flipped[item.id] ? 'ver la pregunta' : 'ver la respuesta' }}</p>
      </button>
    </div>
  </div>
</template>