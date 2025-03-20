<script setup>
import { defineProps, computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [Array, String, Number],
    default: [],
  },
  options: {
    type: Array,
    required: true,
  },
  valueKey: {
    type: String,
    default: 'id',
  },
  labelKey: {
    type: String,
    default: 'nombre_modalidad',
  },
});

const selectedValues = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    // Emit the updated value back to the parent
    emit('update:modelValue', value);
  },
});

const handleCheckboxChange = (optionId) => {
  const currentValue = selectedValues.value;
  if (currentValue.includes(optionId)) {
    selectedValues.value = currentValue.filter(id => id !== optionId);
  } else {
    selectedValues.value.push(optionId);
  }
};
</script>

<template>
  <div class="space-y-2">
    <div
      v-for="option in options"
      :key="option[valueKey]"
      class="flex items-center"
    >
      <input
        type="checkbox"
        :id="option[valueKey]"
        :value="option[valueKey]"
        v-model="selectedValues"
        @change="handleCheckboxChange(option[valueKey])"
        class="mr-2"
      />
      <label :for="option[valueKey]">{{ option[labelKey] }}</label>
    </div>
  </div>
</template>
