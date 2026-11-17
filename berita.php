<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') { http_response_code(410); exit('Fitur berita sudah dihapus.'); }
header('Location: pengumuman', true, 302);
exit();
