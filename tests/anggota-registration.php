<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli('localhost','root','','ksu_st_karolus',3306,'/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
require __DIR__.'/../controller/pendaftaran-anggota.php';
require __DIR__.'/../controller/pemilik-simpanan.php';
foreach (['users','anggota','simpanan','pinjaman'] as $table) {
  $ddl=$conn->query("SHOW CREATE TABLE $table")->fetch_row()[1];
  $ddl=implode("\n",array_filter(explode("\n",$ddl),fn($line)=>strpos($line,'CONSTRAINT')===false));
  $ddl=preg_replace('/,\n\)/',"\n)",$ddl);
  $ddl=str_replace('CREATE TABLE','CREATE TEMPORARY TABLE',$ddl);
  $conn->query(preg_replace('/AUTO_INCREMENT=\d+/','',$ddl));
}
$count=0;
function check($ok,$label) { global $count; if (!$ok) throw new RuntimeException($label); $count++; }
$data=['name'=>'Anggota Uji','nama_lengkap'=>'Anggota Pengujian','email'=>'registration@example.invalid','nik'=>'1234567890123456','no_telp'=>'081234567890','alamat'=>'Alamat pengujian','password'=>'test $ password','re_password'=>'test $ password','id_role'=>1,'id_active'=>1,'jenis_anggota'=>'Tetap'];
$account=buatAkunAnggota($conn,$data);
$row=$conn->query('SELECT a.*,u.id_active,u.id_role,u.password FROM anggota a JOIN users u USING(id_user)')->fetch_assoc();
check($row['nik']===$data['nik'] && $row['alamat']===$data['alamat'] && $row['no_telp']===$data['no_telp'],'Detailed profile persisted');
check($row['jenis_anggota']==='Tidak Tetap','Member cannot set permanent status');
$role=$conn->query('SELECT role FROM user_role WHERE id_role='.(int)$row['id_role'])->fetch_assoc();
check(strtolower($role['role'])==='anggota','Role cannot be escalated');
check((int)$row['id_active']!==1,'Unverified account is inactive');
check(password_verify($data['password'],$row['password']),'Password roundtrip');
foreach ([array_merge($data,['email'=>'bad-email']),array_merge($data,['nik'=>'123']),array_merge($data,['re_password'=>'wrong']),$data,array_merge($data,['email'=>'second@example.invalid'])] as $invalid) {
  try { buatAkunAnggota($conn,$invalid); check(false,'Invalid or duplicate registration accepted'); }
  catch (InvalidArgumentException $e) { check(true,'Invalid/duplicate blocked'); }
}
check((int)$conn->query('SELECT COUNT(*) n FROM users')->fetch_assoc()['n']===1,'Failed registration leaves no extra account');
$conn->begin_transaction();
$id=resolvePemilikSimpanan($conn,'user:999999',true,$account['id_user']);
check($id===(int)$row['id_anggota'],'Member owner comes from session');
tandaiAnggotaTetap($conn,$id); $conn->rollback();
check($conn->query('SELECT jenis_anggota FROM anggota')->fetch_assoc()['jenis_anggota']==='Tidak Tetap','Failed application rolls back promotion');
$conn->begin_transaction();
$id=resolvePemilikSimpanan($conn,'user:'.$account['id_user'],false,0);
tandaiAnggotaTetap($conn,$id);$conn->commit();
check($conn->query('SELECT jenis_anggota FROM anggota')->fetch_assoc()['jenis_anggota']==='Tetap','Successful application promotes member');
check((int)$conn->query('SELECT COUNT(*) n FROM anggota')->fetch_assoc()['n']===1,'Shared resolver does not duplicate profiles');
echo "$count checks passed; no real records or email modified.\n";
