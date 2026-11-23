START TRANSACTION;
UPDATE user_sub_menu SET title='Transaksi Simpanan', url='laporan/transaksi-simpanan'
WHERE url='laporan/transaksi';

INSERT INTO user_sub_menu(id_menu,id_active,title,url)
SELECT source.id_menu,source.id_active,'Transaksi Pinjaman','laporan/transaksi-pinjaman'
FROM user_sub_menu source
WHERE source.url='laporan/transaksi-simpanan'
AND NOT EXISTS (SELECT 1 FROM user_sub_menu WHERE url='laporan/transaksi-pinjaman');

INSERT INTO permissions(id_role,id_menu,id_sub_menu,`view`,`create`,`edit`,`delete`)
SELECT p.id_role,target.id_menu,target.id_sub_menu,p.`view`,p.`create`,p.`edit`,p.`delete`
FROM permissions p
JOIN user_sub_menu source ON source.id_sub_menu=p.id_sub_menu AND source.url='laporan/transaksi-simpanan'
JOIN user_sub_menu target ON target.url='laporan/transaksi-pinjaman'
WHERE NOT EXISTS (SELECT 1 FROM permissions existing WHERE existing.id_role=p.id_role AND existing.id_sub_menu=target.id_sub_menu);
COMMIT;
