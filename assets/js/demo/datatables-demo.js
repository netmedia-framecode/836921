// Check all rows, including rows outside the current page/search results.
function actionCellHasControl(cell) {
  if (!cell) return false;
  return Array.from(cell.querySelectorAll('a[href], button, input[type="button"], input[type="submit"], [role="button"]')).some(control => {
    for (let element = control; element && element !== cell; element = element.parentElement) {
      if (element.hidden || element.classList.contains('d-none') || element.classList.contains('modal') || element.style.display === 'none' || element.style.visibility === 'hidden') return false;
    }
    return true;
  });
}

function updateActionColumns(table) {
  let changed = false;
  table.columns().every(function () {
    const header = this.header();
    if (!header || header.textContent.trim().toLowerCase() !== 'aksi') return;
    const hasActions = this.nodes().toArray().some(actionCellHasControl);
    if (this.visible() !== hasActions) {
      this.visible(hasActions, false);
      changed = true;
    }
  });
  if (changed) table.columns.adjust();
}

if (typeof window !== 'undefined' && window.jQuery) {
  const $ = window.jQuery;
  $(document).on('init.dt draw.dt', function (event, settings) {
    if (settings) updateActionColumns(new $.fn.dataTable.Api(settings));
  });
  $(document).ready(function () {
    $('#dataTable').DataTable();
  });
}
if (typeof module !== 'undefined') module.exports = {actionCellHasControl, updateActionColumns};
