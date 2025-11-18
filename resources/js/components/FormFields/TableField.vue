<template>
<div :class="{ 'no-label': field.removeLabel }">
  <DefaultField
    :field="currentField"
    :errors="errors"
    :show-help-text="showHelpText"
    :full-width-content="currentField.fullWidth"
  >
    <template #field>
      <Table :can-delete="field.canDelete" :edit-mode="!field.readonly">
        <div class="bg-white overflow-hidden key-value-items">
          <TableRow
            v-for="(row, index) in theData"
            :key="row.id"
            :ref="row.id"
            :can-delete="field.canDelete"
            :index="index"
            :read-only="field.readonly"
            :row.sync="row"
            @remove-row="removeRow"
          />
        </div>
      </Table>
      <div v-if="field.canDelete" class="relative mr-12 mt-3 flex">
        <div v-for="n in numberOfColumns" class="flex flex-grow justify-center">
          <button
            class="appearance-none cursor-pointer text-70 hover:text-danger active:outline-none active:shadow-outline focus:outline-none focus:shadow-outline"
            tabindex="-1"
            title="Delete"
            type="button"
            @click="removeColumn(n)"
          >
          <svg height="24" view-box="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M232.7 69.9L224 96L128 96C110.3 96 96 110.3 96 128C96 145.7 110.3 160 128 160L512 160C529.7 160 544 145.7 544 128C544 110.3 529.7 96 512 96L416 96L407.3 69.9C402.9 56.8 390.7 48 376.9 48L263.1 48C249.3 48 237.1 56.8 232.7 69.9zM512 208L128 208L149.1 531.1C150.7 556.4 171.7 576 197 576L443 576C468.3 576 489.3 556.4 490.9 531.1L512 208z"/></svg>
          </button>
        </div>
      </div>
      <div v-if="!field.readonly && field.canAdd" class="mr-12 flex">
        <button
          class="btn btn-link dim cursor-pointer rounded-lg mx-auto text-primary mt-3 px-3 rounded-b-lg flex items-center"
          type="button"
          @click="addRowAndSelect"
        >
          <svg height="24" view-box="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg>
          <span class="ml-1">{{ __('novaTableField.addRow') }}</span>
        </button>
        <button
          v-if="numberOfColumns > 0"
          class="btn btn-link dim cursor-pointer rounded-lg mx-auto text-primary mt-3 px-3 rounded-b-lg flex items-center"
          tabindex="-1"
          type="button"
          @click="addColumnAndSelect"
        >
          <svg height="24" view-box="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M256 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 160-160 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l160 0 0 160c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160 160 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-160 0 0-160z"/></svg>
          <span class="ml-1">{{ __('novaTableField.addColumn') }}</span>
        </button>
      </div>
      <div v-if="field.helpText" class="help-text help-text mt-2" v-html="field.helpText"></div>
    </template>
  </DefaultField>
</div>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import TableRow from './TableRow';
import autosize from 'autosize';
import Table from './Table';

function guid() {
  const S4 = function () {
    return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
  };
  return S4() + S4() + '-' + S4() + '-' + S4() + '-' + S4() + '-' + S4() + S4() + S4();
}

export default {
  mixins: [HandlesValidationErrors, FormField],

  components: { Table, TableRow },

  data: () => ({ theData: [] }),

  mounted() {
    let valuesArray = Array.isArray(this.field.value) ? this.value : JSON.parse(this.field.value);
    if (!Array.isArray(valuesArray) || !valuesArray.length) valuesArray = [];

    if(this.field.defaultValues)
      this.field.defaultValues.forEach((item, index) => {
        if(!valuesArray[index])
          valuesArray[index] = item;
      });
    else
      this.field.defaultValues = [];

    this.theData = _.map(valuesArray, cells => ({
      id: guid(),
      cells,
    }));

    if (this.theData.length === 0) {
      for (let i = 0; i < (this.defaultAttributes.minRows || 1); i++) {
        this.addRow();
      }
    }
  },

  methods: {
    /**
     * Provide a function that fills a passed FormData object with the
     * field's internal value attribute.
     */
    fill(formData) {
      formData.append(this.field.attribute, JSON.stringify(this.finalPayload));
    },

    /**
     * Add a row to the table.
     */
    addRow() {
      if (this.theData.length + 1 > this.defaultAttributes.maxRows)
        return this.$toasted.show(this.__('novaTableField.maxRowsError', { max: this.defaultAttributes.maxRows }), {
          type: 'error',
        });

      return _.tap(guid(), id => {
        this.theData = [...this.theData, { id, cells: Array(this.numberOfColumns).join('.').split('.') }];
        return id;
      });
    },

    /**
     * Add a column to the table.
     */
    addColumn() {
      if (this.numberOfColumns + 1 > this.defaultAttributes.maxColumns)
        return this.$toasted.show(
          this.__('novaTableField.maxColumnsError', { max: this.defaultAttributes.maxColumns }),
          {
            type: 'error',
          }
        );

      this.theData.forEach((_, index) => {
        this.theData[index].cells.push('');
      });
    },

    /**
     * Add a row to the table and select its first field.
     */
    addRowAndSelect() {
      return this.selectRow(this.addRow());
    },

    /**
     * Remove the row from the table.
     */
    removeRow(id) {
      if (this.theData.length - 1 < this.defaultAttributes.minRows)
        return this.$toasted.show(this.__('novaTableField.minRowsError', { min: this.defaultAttributes.minRows }), {
          type: 'error',
        });

      return _.tap(
        _.findIndex(this.theData, row => row.id === id),
        index => this.theData.splice(index, 1)
      );
    },

    /**
     * Remove the column from the table.
     */
    removeColumn(index) {
      if (this.numberOfColumns - 1 < this.defaultAttributes.minColumns)
        return this.$toasted.show(
          this.__('novaTableField.minColumnsError', { min: this.defaultAttributes.minColumns }),
          {
            type: 'error',
          }
        );

      return this.theData.map(row => {
        row.cells.splice(index - 1, 1);
        return row;
      });
    },

    /**
     * Remove all columns and rows from the table.
     */
    removeTable() {
      return (this.theData = []);
    },

    /**
     * Add a column to the row and select its last field.
     */
    addColumnAndSelect() {
      return this.selectColumn(this.addColumn());
    },

    /**
     * Select the first field in a row with the given ref ID.
     */
    selectRow(refId) {
      return this.$nextTick(() => {
        this.$refs[refId][0].$refs.columnFields[0].select();
      });
    },

    /**
     * Select the last cell in the first row.
     */
    selectColumn() {
      return this.$nextTick(() => {
        // prettier-ignore
        Object.values(this.$refs).map(ref => autosize(ref[0].$refs.columnFields))[0].slice(-1)[0].select();
      });
    },
  },

  computed: {
    /**
     * Return the final filtered json object
     */
    finalPayload() {
      return _(this.theData)
        .map(row => (row && row.cells && row.cells.length > 0 ? row.cells : undefined))
        .reject(row => row === undefined)
        .value();
    },

    defaultAttributes() {
      return {
        minRows: this.field.minRows || 1,
        maxRows: this.field.maxRows,
        minColumns: this.field.minColumns || 1,
        maxColumns: this.field.maxColumns,
      };
    },

    numberOfColumns() {
      return this.theData[0] ? this.theData[0].cells.length : this.defaultAttributes.minColumns;
    },
  },
};
</script>
<style>
.no-label > div > div:first-child {
  display: none !important;
}
.no-label > div > div:nth-child(2) {
  width: 100% !important;
  padding-right: 46px !important;
}
</style>
