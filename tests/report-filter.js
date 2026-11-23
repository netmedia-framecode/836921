const assert = require('node:assert/strict');
const {filterReportRows} = require('../assets/js/report-filter.js');
const rows = [
 {nama_lengkap:'Arlan',petugas:'Admin',nominal:1000000,jenis_transaksi:'Simpanan Pokok',tanggal:'2026-11-24 08:00'},
 {nama_lengkap:'Arlan',petugas:'Bendahara',nominal:250000,jenis_transaksi:'Simpanan Wajib',tanggal:'2026-10-24 08:00'},
 {nama_lengkap:'Maria',petugas:'Admin',nominal:500000,jenis_transaksi:'Simpanan Pokok',tanggal:'2026-11-24 08:00'}
];
assert.deepEqual(filterReportRows(rows, []), rows);
assert.deepEqual(filterReportRows(rows, [{key:'nama_lengkap',value:' arLAN '}]), rows.slice(0,2));
assert.deepEqual(filterReportRows(rows, [{key:'nama_lengkap',value:'Arlan'},{key:'petugas',value:'admin'}]), [rows[0]]);
assert.deepEqual(filterReportRows(rows, [{key:'nominal',value:'Rp 1.000.000'}]), [rows[0]]);
assert.deepEqual(filterReportRows(rows, [{custom:true,value:'wajib'}]), [rows[1]]);
assert.deepEqual(filterReportRows(rows, [{key:'tanggal',value:'2026-11'}]), [rows[0],rows[2]]);
assert.deepEqual(filterReportRows(rows, [{key:'nama_lengkap',value:'unknown'}]), []);
assert.deepEqual(filterReportRows(rows, [{key:'petugas',value:''}]), []);
assert.equal(filterReportRows(rows, [{key:'petugas',value:'Bendahara'}])[0], rows[1]);
console.log('9 filter checks passed, complete transaction details preserved.');
