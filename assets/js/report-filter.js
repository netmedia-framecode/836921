/* Field filters are combined with AND; custom keywords search transaction details. */
function filterReportRows(rows, filters) {
  const text = value => String(value ?? '').trim().toLocaleLowerCase('id-ID');
  const numeric = value => Number(String(value).replace(/rp\.?\s*/gi, '').replace(/\s/g, '').replace(/\./g, '').replace(',', '.'));
  return rows.filter(row => filters.every(filter => {
    const value = text(filter.value);
    if (!value) return false;
    if (filter.custom) {
      return Object.values(row).some(cell => text(cell).includes(value));
    }
    if (filter.key === 'nominal') {
      const amount = numeric(value);
      return Number.isFinite(amount) && Number(row.nominal) === amount;
    }
    return text(row[filter.key]).includes(value);
  }));
}
if (typeof module !== 'undefined') module.exports = {filterReportRows};
