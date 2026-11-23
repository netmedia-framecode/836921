const assert = require('node:assert/strict');
const {actionCellHasControl, updateActionColumns} = require('../assets/js/demo/datatables-demo.js');
function cell(classes = null) {
 const result = {querySelectorAll: () => classes === null ? [] : [control]};
 const control = {hidden:false, classList:{contains:name=>classes.includes(name)}, style:{}, parentElement:result};
 return result;
}
assert.equal(actionCellHasControl(cell()), false);
assert.equal(actionCellHasControl(cell([])), true);
assert.equal(actionCellHasControl(cell(['d-none'])), false);
assert.equal(actionCellHasControl(cell(['modal'])), false);
let visible=true, adjustments=0, nodes=[cell(),cell()];
const action={header:()=>({textContent:' Aksi '}),nodes:()=>({toArray:()=>nodes}),visible(value){if(value!==undefined)visible=value;return visible;}};
const name={header:()=>({textContent:'Nama'}),nodes(){throw Error('Other columns must be unchanged');}};
const table={columns:()=>({every(fn){[name,action].forEach(col=>fn.call(col));}})};
table.columns.adjust=()=>adjustments++;
updateActionColumns(table); assert.equal(visible,false);
// A control on a later page must keep the column visible even if search hides it.
nodes=[cell(),cell(),cell([])];updateActionColumns(table);assert.equal(visible,true);
updateActionColumns(table);assert.equal(adjustments,2);
nodes=[];updateActionColumns(table);assert.equal(visible,false);
console.log('8 action-column checks passed.');
