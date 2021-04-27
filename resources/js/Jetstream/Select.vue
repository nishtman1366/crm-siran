<template>
    <select ref="select" :value="value" class="form-input rounded-md shadow-sm pr-7"
            @input="$emit('input', $event.target.value)">
        <option v-for="option in options" :key="option[optionValue]" :value="option[optionValue]">
            {{option[optionText]}}
        </option>
    </select>
</template>

<script>
    export default {
        props: {
            value: String,
            options: Array,
            optionValue: {
                type: String,
                default: 'id'
            },
            optionText: {
                type: String,
                default: 'name'
            }
        },

        methods: {
            focus() {
                this.$refs.select.focus()
            }
        }
    }
</script>

