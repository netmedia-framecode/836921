<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn=new mysqli('localhost','root','','ksu_st_karolus',3306,'/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
require __DIR__.'/../controller/profil-anggota.php';
foreach (['users','user_role','anggota'] as $table) {
 $ddl=$conn->query("SHOW CREATE TABLE $table")->fetch_row()[1];
 $ddl=implode("\n",array_filter(explode("\n",$ddl),fn($line)=>strpos($line,'CONSTRAINT')===false));
 $ddl=preg_replace('/,\n\)/',"\n)",$ddl);
 $conn->query(preg_replace('/AUTO_INCREMENT=\d+/','',str_replace('CREATE TABLE','CREATE TEMPORARY TABLE',$ddl)));
}
$conn->query("INSERT INTO user_role(id_role,role) VALUES(1,'Developer'),(5,'Anggota')");
$conn->query("INSERT INTO users(id_user,id_role,name,created_at) VALUES(1,1,'Staff','2026-01-01'),(2,5,'Member','2026-01-01'),(3,5,'Other','2026-01-01'),(4,5,'Legacy','2026-01-01')");
$conn->query("INSERT INTO anggota(id_anggota,id_user,nik,nama_lengkap,jenis_anggota,tgl_gabung) VALUES(1,2,'1234567890123456','Before','Tetap','2026-01-01'),(2,3,'2234567890123456','Other','Tidak Tetap','2026-01-01')");
$count=0;
function checkProfile($ok){global $count;if(!$ok)throw new RuntimeException('Profile regression failed');$count++;}
$data=['nama_lengkap'=>"Member O'Neil",'nik'=>'3234567890123456','no_telp'=>'081234567890','alamat'=>'Jl. Mawar 1','id_user'=>3,'id_anggota'=>2,'jenis_anggota'=>'Tidak Tetap'];
simpanProfilAnggota($conn,2,$data);
$row=$conn->query('SELECT * FROM anggota WHERE id_user=2')->fetch_assoc();
checkProfile($row['nik']===$data['nik'] && html_entity_decode($row['nama_lengkap'],ENT_QUOTES)===$data['nama_lengkap']);
checkProfile($row['jenis_anggota']==='Tetap' && $row['tgl_gabung']==='2026-01-01');
checkProfile($conn->query('SELECT nik FROM anggota WHERE id_user=3')->fetch_row()[0]==='2234567890123456');
simpanProfilAnggota($conn,2,$data);checkProfile(true);
foreach ([['nik'=>'2234567890123456'],['nik'=>'bad'],['no_telp'=>'mail@example.com'],['nama_lengkap'=>''],['alamat'=>'']] as $bad) {
 $blocked=false;try{simpanProfilAnggota($conn,2,array_merge($data,$bad));}catch(InvalidArgumentException $e){$blocked=true;}
 checkProfile($blocked && $conn->query('SELECT nik FROM anggota WHERE id_user=2')->fetch_row()[0]===$data['nik']);
}
$blocked=false;try{simpanProfilAnggota($conn,1,$data);}catch(RuntimeException $e){$blocked=true;}checkProfile($blocked);
simpanProfilAnggota($conn,4,array_merge($data,['nik'=>'4234567890123456']));
checkProfile((int)$conn->query('SELECT COUNT(*) FROM anggota WHERE id_user=4')->fetch_row()[0]===1);
echo "$count profile checks passed; real data unchanged.\n";
