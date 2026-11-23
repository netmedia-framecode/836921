<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn=new mysqli('localhost','root','','ksu_st_karolus',3306,'/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
require __DIR__.'/../controller/tambah-user.php';
foreach (['users','user_role','user_status','permissions','user_sub_menu'] as $table) {
  $ddl=$conn->query("SHOW CREATE TABLE $table")->fetch_row()[1];
  $ddl=implode("\n",array_filter(explode("\n",$ddl),fn($line)=>strpos($line,'CONSTRAINT')===false));
  $ddl=preg_replace('/,\n\)/',"\n)",$ddl);
  $ddl=str_replace('CREATE TABLE','CREATE TEMPORARY TABLE',$ddl);
  $conn->query(preg_replace('/AUTO_INCREMENT=\d+/','',$ddl));
}
$conn->query("INSERT INTO user_role(id_role,role) VALUES(1,'Administrator'),(2,'Ketua Koperasi'),(3,'Bendahara'),(4,'Anggota'),(7,'Developer'),(8,'Auditor')");
$conn->query("INSERT INTO user_status(id_status,status) VALUES(1,'Active'),(2,'No Active')");
$hash=password_hash('creator-secret',PASSWORD_DEFAULT);
$stmt=$conn->prepare('INSERT INTO users(id_user,id_role,id_active,name,email,password) VALUES(?,?,?,?,?,?)');
foreach ([[1,1,1,'Admin','admin@example.invalid'],[2,3,1,'Staff','staff@example.invalid'],[3,4,1,'Member','member@example.invalid'],[4,1,2,'Inactive','inactive@example.invalid']] as $u) {
 $stmt->bind_param('iiisss',$u[0],$u[1],$u[2],$u[3],$u[4],$hash);$stmt->execute();
}
$stmt->close();
$conn->query("INSERT INTO user_sub_menu(id_sub_menu,url) VALUES(1,'manajemen-pengguna/users')");
$conn->query('INSERT INTO permissions(id_role,id_sub_menu,`create`) VALUES(3,1,1)');
$count=0;
function check($ok,$label){global $count;if(!$ok)throw new RuntimeException($label);$count++;}
function totalUsers($conn){return (int)$conn->query('SELECT COUNT(*) n FROM users')->fetch_assoc()['n'];}
$data=['name'=>'New User','email'=>'new@example.invalid','password'=>'New pass $ & 123','re_password'=>'New pass $ & 123','id_role'=>'2','id_active'=>'1','password_pembuat'=>'creator-secret'];
$bad=[
 [1,array_merge($data,['password_pembuat'=>'wrong'])],
 [1,array_diff_key($data,['password_pembuat'=>1])],
 [3,array_merge($data,['id_user'=>1])],
 [4,$data],
 [1,array_merge($data,['email'=>'bad'])],
 [1,array_merge($data,['id_role'=>'8'])],
 [1,array_merge($data,['id_role'=>'4'])],
 [1,array_merge($data,['id_role'=>'999'])],
 [1,array_merge($data,['id_active'=>'999'])],
 [1,array_merge($data,['re_password'=>'different'])],
];
foreach($bad as [$actor,$input]){
 $before=totalUsers($conn);$blocked=false;
 try{buatUserDenganKonfirmasi($conn,$actor,$input);}catch(Throwable $e){$blocked=true;}
 check($blocked && totalUsers($conn)===$before,'Invalid request must not insert');
}
$id=buatUserDenganKonfirmasi($conn,1,$data);
$row=$conn->query('SELECT * FROM users WHERE id_user='.(int)$id)->fetch_assoc();
check($row['email']===$data['email'] && (int)$row['id_role']===2,'Valid admin creation');
check(password_verify($data['password'],$row['password']) && $row['password']!==$data['password'],'New password hashed');
check(!password_verify('creator-secret',$row['password']),'Creator password not stored as new password');
$before=totalUsers($conn);$blocked=false;
try{buatUserDenganKonfirmasi($conn,1,$data);}catch(InvalidArgumentException $e){$blocked=true;}
check($blocked && totalUsers($conn)===$before,'Duplicate email blocked');
$id=buatUserDenganKonfirmasi($conn,2,array_merge($data,['email'=>'staff-created@example.invalid']));
check($id>0,'Staff with create permission can create');
$conn->query('UPDATE permissions SET `create`=0 WHERE id_role=3');
$before=totalUsers($conn);$blocked=false;
try{buatUserDenganKonfirmasi($conn,2,array_merge($data,['email'=>'revoked@example.invalid']));}catch(RuntimeException $e){$blocked=true;}
check($blocked && totalUsers($conn)===$before,'Revoked permission checked against database');
foreach ([1,2,3,7] as $selectedRole) {
 $id=buatUserDenganKonfirmasi($conn,1,array_merge($data,['id_role'=>(string)$selectedRole,'email'=>'role-'.$selectedRole.'@example.invalid']));
 $savedRole=(int)$conn->query('SELECT id_role FROM users WHERE id_user='.(int)$id)->fetch_row()[0];
 check($savedRole===$selectedRole,'Selected staff role preserved');
}
echo "$count checks passed; real users unchanged.\n";
