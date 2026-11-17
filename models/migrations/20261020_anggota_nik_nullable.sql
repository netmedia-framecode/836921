-- Self-registered users have not provided a NIK yet.
-- Keep the unique index for known NIK values.
ALTER TABLE anggota MODIFY nik VARCHAR(20) NULL;
