<script setup>
import { ref, computed } from 'vue'

const props = defineProps({ quiz: { type: Object, required: true } })

const answers = ref({})
const submitted = ref(false)

const score = computed(() => {
  if (!submitted.value) return null
  const correct = props.quiz.questions.filter(
    (q) => answers.value[q.id] === q.correct_option
  ).length
  return { correct, total: props.quiz.questions.length }
})

function optionLetter(index) {
  return ['A', 'B', 'C', 'D', 'E'][index]
}
</script>

<template>
  <div class="flex flex-col gap-6">
    <div v-for="question in quiz.questions" :key="question.id" class="card p-5">
      <p class="font-medium text-ink">{{ question.question }}</p>

      <div class="mt-3 flex flex-col gap-2">
        <label
          v-for="(option, i) in question.options"
          :key="i"
          class="flex cursor-pointer items-center gap-3 rounded-lg border px-3.5 py-2.5 text-sm transition"
          :class="[
            answers[question.id] === optionLetter(i) ? 'border-teal bg-teal/5' : 'border-border',
            submitted && optionLetter(i) === question.correct_option ? '!border-teal !bg-teal/10' : '',
            submitted && answers[question.id] === optionLetter(i) && optionLetter(i) !== question.correct_option ? '!border-red-400 !bg-red-400/10' : '',
          ]"
        >
          <input
            type="radio"
            :name="`q-${question.id}`"
            class="accent-teal"
            :disabled="submitted"
            :checked="answers[question.id] === optionLetter(i)"
            @change="answers[question.id] = optionLetter(i)"
          />
          <span class="text-ink/90">{{ option }}</span>
        </label>
      </div>

      <p v-if="submitted && question.explanation" class="mt-3 rounded-lg bg-elevated px-3.5 py-2.5 text-xs text-muted">
        {{ question.explanation }}
      </p>
    </div>

    <div class="flex items-center gap-4">
      <button v-if="!submitted" type="button" class="btn-primary" @click="submitted = true">Corregir</button>
      <p v-else class="font-display text-lg">
        Resultado: <span class="text-teal">{{ score.correct }}/{{ score.total }}</span>
      </p>
    </div>
  </div>
</template>