<template>
  <div class="flex items-center key-value-item">
    <div class="flex flex-grow border-b border-50 key-value-fields">
      <div
        :key="`column-${index}`"
        @click="handleColumnFieldFocus(index)"
        class="flex-grow border-l border-50"
        v-for="(cell, index) in row.cells"
      >
        <textarea
          :class="{
            'bg-white': !isEditable,
            'hover:bg-20 focus:bg-white': isEditable,
          }"
          :disabled="!isEditable"
          :dusk="`key-value-value-${index}`"
          :key="cell.id"
          @focus="handleColumnFieldFocus(index)"
          class="font-mono text-sm block min-h-input w-full form-control form-input form-input-row py-4 text-90 min-h-full"
          ref="columnFields"
          v-model="row.cells[index]"
        />
      </div>
    </div>

    <div class="flex justify-center h-11 w-11 absolute" style="right: -40px" v-if="isEditable && canDelete">
      <button
        @click="$emit('remove-row', row.id)"
        class="flex appearance-none cursor-pointer text-70 hover:text-danger active:outline-none active:shadow-outline focus:outline-none focus:shadow-outline"
        style="align-items: center"
        tabindex="-1"
        title="Delete"
        type="button"
      >
        <svg height="24" view-box="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M232.7 69.9L224 96L128 96C110.3 96 96 110.3 96 128C96 145.7 110.3 160 128 160L512 160C529.7 160 544 145.7 544 128C544 110.3 529.7 96 512 96L416 96L407.3 69.9C402.9 56.8 390.7 48 376.9 48L263.1 48C249.3 48 237.1 56.8 232.7 69.9zM512 208L128 208L149.1 531.1C150.7 556.4 171.7 576 197 576L443 576C468.3 576 489.3 556.4 490.9 531.1L512 208z"/></svg>
      </button>
    </div>
  </div>
</template>

<script>
import autosize from 'autosize';

export default {
  props: {
    index: Number,
    row: Object,
    disabled: {
      type: Boolean,
      default: false,
    },
    readOnly: {
      type: Boolean,
      default: false,
    },
    canDelete: {
      type: Boolean,
      default: true,
    },
  },

  mounted() {
    autosize(this.$refs.columnFields);
  },

  methods: {
    handleColumnFieldFocus(index) {
      this.$refs.columnFields[index].select();
    },
  },

  computed: {
    isEditable() {
      return !this.readOnly && !this.disabled;
    },
  },
};
</script>
