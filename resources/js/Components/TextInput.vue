<script setup>
import { onMounted, ref, computed } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    class: {
        type: String,
        default: 'rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
    }
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input :class="props.class" v-model="model" ref="input" />
</template>
