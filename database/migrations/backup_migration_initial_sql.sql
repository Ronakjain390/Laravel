CREATE TABLE `additional_features` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `additional_feature_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_id` int unsigned NOT NULL,
  `panel_id` int unsigned NOT NULL,
  `panel_feature_id` int unsigned NOT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `additional_features_section_id_foreign` (`section_id`),
  KEY `additional_features_panel_id_foreign` (`panel_id`),
  KEY `additional_features_panel_feature_id_foreign` (`panel_feature_id`),
  CONSTRAINT `additional_features_panel_feature_id_foreign` FOREIGN KEY (`panel_feature_id`) REFERENCES `panel_features` (`id`),
  CONSTRAINT `additional_features_panel_id_foreign` FOREIGN KEY (`panel_id`) REFERENCES `panel` (`id`),
  CONSTRAINT `additional_features_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admins` (
  `id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `dashboard` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `dashboard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
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

CREATE TABLE `features` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `feature_type_id` int unsigned DEFAULT NULL,
  `panel_feature_id` int unsigned DEFAULT NULL,
  `feature_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `features_feature_type_id_foreign` (`feature_type_id`),
  KEY `features_panel_feature_id_foreign` (`panel_feature_id`),
  CONSTRAINT `features_feature_type_id_foreign` FOREIGN KEY (`feature_type_id`) REFERENCES `feature_type` (`id`),
  CONSTRAINT `features_panel_feature_id_foreign` FOREIGN KEY (`panel_feature_id`) REFERENCES `panel_features` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `feature_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `feature_type_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `panel` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `panel_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section_id` int unsigned DEFAULT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `panel_section_id_foreign` (`section_id`),
  CONSTRAINT `panel_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `panel_columns` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `panel_column_default_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `panel_column_display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `panel_id` int unsigned NOT NULL,
  `section_id` int unsigned NOT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `panel_columns_user_id_foreign` (`user_id`),
  KEY `panel_columns_panel_id_foreign` (`panel_id`),
  KEY `panel_columns_section_id_foreign` (`section_id`),
  CONSTRAINT `panel_columns_panel_id_foreign` FOREIGN KEY (`panel_id`) REFERENCES `panel` (`id`),
  CONSTRAINT `panel_columns_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`),
  CONSTRAINT `panel_columns_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `panel_design` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `feature_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feature_id` int unsigned DEFAULT NULL,
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_id` int unsigned NOT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `panel_design_feature_id_foreign` (`feature_id`),
  KEY `panel_design_template_id_foreign` (`template_id`),
  CONSTRAINT `panel_design_feature_id_foreign` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `panel_design_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `panel_features` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `panel_feature_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `panel_id` int unsigned DEFAULT NULL,
  `section_id` int unsigned DEFAULT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `panel_features_panel_id_foreign` (`panel_id`),
  KEY `panel_features_section_id_foreign` (`section_id`),
  CONSTRAINT `panel_features_panel_id_foreign` FOREIGN KEY (`panel_id`) REFERENCES `panel` (`id`),
  CONSTRAINT `panel_features_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pdf_template` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pdf_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdf_template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `section` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `teams` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `team_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_owner_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_owner_user_id` bigint unsigned DEFAULT NULL,
  `team_name_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teams_team_owner_user_id_foreign` (`team_owner_user_id`),
  CONSTRAINT `teams_team_owner_user_id_foreign` FOREIGN KEY (`team_owner_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `team_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `team_user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `team_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `team_user_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_user_address` longtext COLLATE utf8mb4_unicode_ci,
  `team_user_pincode` int DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `team_user_state` varchar(75) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_user_city` varchar(75) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_id` int unsigned NOT NULL,
  `team_owner_user_id` bigint unsigned NOT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `team_users_team_id_foreign` (`team_id`),
  KEY `team_users_team_owner_user_id_foreign` (`team_owner_user_id`),
  CONSTRAINT `team_users_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`),
  CONSTRAINT `team_users_team_owner_user_id_foreign` FOREIGN KEY (`team_owner_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template_page_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','pause','terminated') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `special_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `pincode` int DEFAULT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pancard` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(75) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(75) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ifsc_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tan` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','pause','terminate') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `first_time` tinyint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_phone_unique` (`phone`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
