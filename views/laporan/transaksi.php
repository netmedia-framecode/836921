<?php
header('Location: transaksi-simpanan' . (isset($_GET['export_transaksi_excel']) ? '?export_transaksi_excel=1' : ''));
exit;
