-- The application explicitly supplies id_role for staff and member registration.
-- This legacy trigger overwrites that selection with the highest role ID.
DROP TRIGGER IF EXISTS insert_users;
