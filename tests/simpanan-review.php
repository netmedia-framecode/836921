<?php
// Temporary tables shadow real tables on this connection only.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli('localhost', 'root', '', 'ksu_st_karolus', 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
$_SESSION['project_ksu_st_karolus']['users'] = ['id'=>1];
require __DIR__.'/../controller/Functions.php';
require __DIR__.'/../controller/persetujuan-simpanan.php';
foreach (['simpanan','pinjaman','angsuran'] as $table) {
  $ddl = $conn->query("SHOW CREATE TABLE $table")->fetch_row()[1];
  $ddl = implode("\n", array_filter(explode("\n", $ddl), function ($line) { return strpos($line, 'CONSTRAINT') === false; }));
  $ddl = preg_replace('/,\n\)/', "\n)", $ddl);
  $ddl = str_replace('CREATE TABLE', 'CREATE TEMPORARY TABLE', $ddl);
  $ddl = preg_replace('/AUTO_INCREMENT=\d+/', '', $ddl);
  $conn->query($ddl);
}
$count=0;
function check($value,$message) { global $count; if (!$value) throw new RuntimeException($message); $count++; }
$data=['id_anggota'=>1,'jenis_simpanan'=>'Simpanan Wajib','jumlah'=>50000,'tgl_simpanan'=>'2026-11-03 10:00:00','id_rat'=>1];
check(validasiDataSimpanan($data),'Valid deposit');
foreach ([-1,0] as $amount) check(!validasiDataSimpanan(array_merge($data,['jumlah'=>$amount])),'Invalid amount');
check(!validasiDataSimpanan(array_merge($data,['jenis_simpanan'=>'Other'])),'Invalid type');
check(simpanan($conn,$data,'insert')===1,'Create request');
check(!reviewSimpanan($conn,1,'Disetujui','',2,'Ketua Koperasi'),'Head cannot skip verification');
check(!reviewSimpanan($conn,1,'Diverifikasi','',2,'Anggota'),'Member cannot verify');
check(!reviewSimpanan($conn,1,'Disetujui','',2,'Administrator'),'Admin cannot approve');
check(reviewSimpanan($conn,1,'Diverifikasi','KTP dan foto lengkap',2,'Bendahara'),'Treasurer verifies');
check(!reviewSimpanan($conn,1,'Disetujui','',2,'Bendahara'),'Treasurer cannot give final approval');
check(!reviewSimpanan($conn,1,'Ditolak','',3,'Ketua Koperasi'),'Rejection requires reason');
check(reviewSimpanan($conn,1,'Disetujui','ACC',3,'Ketua Koperasi'),'Head approves');
check(!reviewSimpanan($conn,1,'Ditolak','ulang',3,'Ketua Koperasi'),'No repeated decision');
simpanan($conn,array_merge($data,['id_simpanan'=>1,'jumlah'=>99999]),'update');
check(simpanan($conn,['id_simpanan'=>1],'delete')===0,'Approved record locked');
$row=$conn->query('SELECT * FROM simpanan WHERE id_simpanan=1')->fetch_assoc();
check((float)$row['jumlah']===50000.0 && (int)$row['id_reviewer']===3 && (int)$row['id_pemeriksa']===2 && (int)$row['id_penerima']===2,'Review audit and amount preserved');
simpanan($conn,$data,'insert');
check(reviewSimpanan($conn,2,'Diverifikasi','Lengkap',2,'Administrator'),'Admin verifies');
check(reviewSimpanan($conn,2,'Ditolak','Belum memenuhi ketentuan',3,'Kepala Koperasi'),'Head rejects');
simpanan($conn,$data,'insert');
$row=$conn->query("SELECT SUM(jumlah) total FROM simpanan WHERE status_simpanan='Disetujui'")->fetch_assoc();
check((float)$row['total']===50000.0,'Only approved amounts in balance');
$loan=['id_anggota'=>1,'id_validator'=>'','jumlah_pinjaman'=>1000000,'tenor_bulan'=>12,'bunga_persen'=>6,'status_pinjam'=>'Pengajuan','tanggal_pengajuan'=>'2026-11-03','tanggal_disetujui'=>'','id_rat'=>1];
check(pinjaman($conn,$loan,'insert')===1,'Create RAT loan');
check(!prosesReviewPengajuan($conn,'pinjaman',1,'Disetujui','',3,'Ketua Koperasi'),'Loan cannot skip review');
check(prosesReviewPengajuan($conn,'pinjaman',1,'Diverifikasi','Berkas lengkap',2,'Administrator'),'Loan review');
check(!prosesReviewPengajuan($conn,'pinjaman',1,'Disetujui','',2,'Administrator'),'Admin cannot approve loan');
check(prosesReviewPengajuan($conn,'pinjaman',1,'Disetujui','ACC',3,'Ketua Koperasi'),'Loan ACC');
check(pinjaman($conn,['id_pinjaman'=>1],'delete')===0,'Approved loan cannot be deleted');
$row=$conn->query('SELECT * FROM pinjaman WHERE id_pinjaman=1')->fetch_assoc();
check((int)$row['id_validator']===3 && $row['tanggal_disetujui'] && $row['basis_bunga']==='total' && (int)$row['id_rat']===1,'Loan terms snapshot and approver');
check(totalTagihanPinjaman(1000000,6,12,'total')===1060000.0,'6 percent total is 60000');
check(totalTagihanPinjaman(1000000,6,12,'bulanan')===1720000.0,'Legacy monthly basis unchanged');
$sum=$conn->query('SELECT '.sqlTotalTagihanPinjaman().' total FROM pinjaman')->fetch_assoc();
check((float)$sum['total']===1060000.0,'Dashboard SQL matches PHP total');
for ($i=1;$i<=12;$i++) {
  check(angsuran($conn, ['id_pinjaman'=>1,'id_penerima'=>2,'angsuran_ke'=>$i,'auto_hitung_angsuran'=>1,'tgl_bayar'=>'2026-11-03 10:00:00'], 'insert')===1, 'Monthly installment saved');
}
$paid=$conn->query('SELECT SUM(jumlah_bayar) total FROM angsuran WHERE id_pinjaman=1')->fetch_assoc();
$last=$conn->query('SELECT sisa_pinjaman FROM angsuran ORDER BY id_angsuran DESC LIMIT 1')->fetch_assoc();
check((float)$paid['total']===1060000.0 && (float)$last['sisa_pinjaman']===0.0, 'Final installment clears rounding residue');
echo "$count checks passed. Real records unchanged.\n";
