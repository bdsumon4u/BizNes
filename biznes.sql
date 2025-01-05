-- -------------------------------------------------------------
-- TablePlus 6.2.1(578)
--
-- https://tableplus.com/
--
-- Database: biznes
-- Generation Time: 2025-01-05 18:58:29.9900
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


CREATE TABLE `business_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `business_user_business_id_foreign` (`business_id`),
  KEY `business_user_user_id_foreign` (`user_id`),
  CONSTRAINT `business_user_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `business_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `businesses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `businesses_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `team_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`team_id`,`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  KEY `model_has_permissions_permission_id_foreign` (`permission_id`),
  KEY `model_has_permissions_team_foreign_key_index` (`team_id`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `team_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`team_id`,`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  KEY `model_has_roles_role_id_foreign` (`role_id`),
  KEY `model_has_roles_team_foreign_key_index` (`team_id`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_team_id_name_guard_name_unique` (`team_id`,`name`,`guard_name`),
  KEY `roles_team_foreign_key_index` (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `social_provider_user` (
  `user_id` bigint unsigned NOT NULL,
  `provider_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_data` text COLLATE utf8mb4_unicode_ci,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `refresh_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`provider_slug`),
  CONSTRAINT `social_provider_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `business_user` (`id`, `business_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 1, NULL, NULL),
(3, 3, 2, NULL, NULL),
(5, 5, 2, NULL, NULL);

INSERT INTO `businesses` (`id`, `uuid`, `name`, `created_at`, `updated_at`) VALUES
(1, 'c6e65978-6a74-4b5c-a20b-26d2ce214e86', 'Business ONe', '2025-01-05 12:25:06', '2025-01-05 12:25:06'),
(2, '4ab3ed43-9d31-48ec-9e1d-116b8f4bc52d', 'Business TWo', '2025-01-05 12:25:15', '2025-01-05 12:25:15'),
(3, '58354993-f204-4a7b-922c-27e987de0371', 'TestBiz', '2025-01-05 12:38:12', '2025-01-05 12:38:12'),
(5, 'a86d5396-3ed5-420a-9ab1-08492b505ba3', 'BizTest', '2025-01-05 12:51:16', '2025-01-05 12:51:16');

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('356a192b7913b04c54574d18c28d46e6395428ab', 'i:1;', 1736080728),
('356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1736080728;', 1736080728),
('da4b9237bacccdf19c0760cab7aec4a8359010b0', 'i:1;', 1736080745),
('da4b9237bacccdf19c0760cab7aec4a8359010b0:timer', 'i:1736080745;', 1736080745),
('spatie.permission.cache', 'a:3:{s:5:\"alias\";a:5:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";s:1:\"j\";s:7:\"team_id\";}s:11:\"permissions\";a:6:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:9:\"view_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:13:\"view_any_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:11:\"create_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:11:\"update_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:11:\"delete_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:15:\"delete_any_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}}s:5:\"roles\";a:3:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"j\";N;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}i:1;a:4:{s:1:\"a\";i:2;s:1:\"j\";i:1;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}i:2;a:4:{s:1:\"a\";i:3;s:1:\"j\";i:3;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}}}', 1736167876);

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_04_24_000001_add_user_social_provider_table', 1),
(5, '2024_04_24_000002_update_passwords_field_to_be_nullable', 1),
(6, '2024_05_07_000003_add_two_factor_auth_columns', 1),
(7, '2025_01_05_121318_create_businesses_table', 2),
(9, '2025_01_05_123056_create_permission_tables', 3);

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`, `team_id`) VALUES
(2, 'App\\Models\\User', 1, 1),
(3, 'App\\Models\\User', 2, 3),
(5, 'App\\Models\\User', 2, 5);

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view_role', 'web', '2025-01-05 12:32:35', '2025-01-05 12:32:35'),
(2, 'view_any_role', 'web', '2025-01-05 12:32:35', '2025-01-05 12:32:35'),
(3, 'create_role', 'web', '2025-01-05 12:32:35', '2025-01-05 12:32:35'),
(4, 'update_role', 'web', '2025-01-05 12:32:35', '2025-01-05 12:32:35'),
(5, 'delete_role', 'web', '2025-01-05 12:32:35', '2025-01-05 12:32:35'),
(6, 'delete_any_role', 'web', '2025-01-05 12:32:35', '2025-01-05 12:32:35');

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(3, 3),
(4, 1),
(4, 2),
(4, 3),
(5, 1),
(5, 2),
(5, 3),
(6, 1),
(6, 2),
(6, 3);

INSERT INTO `roles` (`id`, `team_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, NULL, 'super_admin', 'web', '2025-01-05 12:32:35', '2025-01-05 12:32:35'),
(2, 1, 'super_admin', 'web', '2025-01-05 12:35:26', '2025-01-05 12:35:26'),
(3, 3, 'super_admin', 'web', '2025-01-05 12:38:39', '2025-01-05 12:38:39'),
(5, 5, 'super_admin', 'web', '2025-01-05 12:51:16', '2025-01-05 12:51:16');

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0vM1bUpENqaEw25MiVOJpbfIMVSTRv6H4cLrAmob', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoidUJzM3czbEZsWGl6djUyVDU0M2dSdWhMbWlZYWg0T1lrVVh2NmxocyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE3MzoiaHR0cDovL2Jpem5lcy50ZXN0L2F1dGgvdmVyaWZ5LWVtYWlsLzIvYTZhZDAwYWMxMTNhMTlkOTUzZWZiOTE4MjBkODc4OGUyMjYzYjI4YT9leHBpcmVzPTE3MzYwODQyNDEmc2lnbmF0dXJlPTEzYzIwNjhlYzhlNjkzYmFlYjQxZmJlMDQ2OWI2MTM5YzUxNmEyMjllOTNhNDQ2MDIxNDI4YWU4M2I3ZTgzMjEiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkS0doQ3VkeUdaWW01OURZd3dmZEp2LjNYelU4TmhZZEVPVDg3a1FOQ0hHUDVrc1pjUk1NR3EiO30=', 1736080668),
('4DQNKuKFx4yNnHQyoUdUu6q6BXoR30TZV1y6kRXX', 2, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiWjhHd0Iwc053VzFrOENkRG9Ucjd5Mk93cXhqTEppMG9EOFpyOWliSCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjYyOiJodHRwOi8vYml6bmVzLnRlc3Qvc3R1ZGlvLzU4MzU0OTkzLWYyMDQtNGE3Yi05MjJjLTI3ZTk4N2RlMDM3MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiQ1NkQ1WlNyTnRDWWo5Qm81eU16UE51UXZWRTlJbVF4VWV5ZFJCaU83Q1F5WUUuQTJqYnVHVyI7fQ==', 1736081707);

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Sumon Ahmed', 'bdsumon4u@gmail.com', '2025-01-05 11:27:52', '$2y$12$KGhCudyGZYm59DYwwfdJv.3XzU8NhYdEOT87kQNCHGP5ksZcRMMGq', NULL, NULL, NULL, 'uu9ndpNh5iPdiTIe1e37wsZOn87uGmogUluPiXW1mhVVtzIzsZQQakBtctPE', '2025-01-05 11:25:23', '2025-01-05 11:27:52'),
(2, 'test', 'test@test.com', '2025-01-05 12:38:05', '$2y$12$56D5ZSrNtCYj9Bo5yMzPNuQvVE9ImQxUeydRBiO7CQyYE.A2jbuGW', NULL, NULL, NULL, 'tnYUXGkj8BjCWwtyjCziwISzEyW2BlIzZ6ey5XjrEPkHLN40S3eZO5teEZLy', '2025-01-05 12:37:21', '2025-01-05 12:38:05');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;