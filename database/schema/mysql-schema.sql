/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_items_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'popular',
  `can_users_create` tinyint(1) NOT NULL DEFAULT '0',
  `block_comments` tinyint(1) NOT NULL DEFAULT '0',
  `block_votes` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `boards_project_id_foreign` (`project_id`),
  KEY `boards_slug_index` (`slug`),
  CONSTRAINT `boards_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `changelog_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `changelog_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `changelog_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `changelog_item_changelog_id_foreign` (`changelog_id`),
  KEY `changelog_item_item_id_foreign` (`item_id`),
  CONSTRAINT `changelog_item_changelog_id_foreign` FOREIGN KEY (`changelog_id`) REFERENCES `changelogs` (`id`),
  CONSTRAINT `changelog_item_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `changelogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `changelogs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `published_at` datetime DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `changelogs_user_id_foreign` (`user_id`),
  KEY `changelogs_slug_index` (`slug`),
  CONSTRAINT `changelogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `total_votes` int(10) unsigned DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_item_id_foreign` (`item_id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_parent_id_foreign` (`parent_id`),
  CONSTRAINT `comments_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`),
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `item_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_user` (
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  KEY `item_user_item_id_foreign` (`item_id`),
  KEY `item_user_user_id_foreign` (`user_id`),
  CONSTRAINT `item_user_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `item_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `total_votes` int(10) unsigned DEFAULT '0',
  `pinned` tinyint(1) NOT NULL DEFAULT '0',
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `notify_subscribers` tinyint(1) NOT NULL DEFAULT '1',
  `project_id` int(11) DEFAULT NULL,
  `board_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `issue_number` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `items_board_id_foreign` (`board_id`),
  KEY `items_user_id_foreign` (`user_id`),
  KEY `items_slug_index` (`slug`),
  CONSTRAINT `items_board_id_foreign` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`),
  CONSTRAINT `items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mentions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mentions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `recipient_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mentions_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `mentions_recipient_type_recipient_id_index` (`recipient_type`,`recipient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_member` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_member_user_id_foreign` (`user_id`),
  KEY `project_member_project_id_foreign` (`project_id`),
  CONSTRAINT `project_member_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_member_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `repo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_slug_index` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `payload` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_group_name_unique` (`group`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `taggables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taggables` (
  `tag_id` bigint(20) unsigned NOT NULL,
  `taggable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taggable_id` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `taggables_tag_id_taggable_id_taggable_type_unique` (`tag_id`,`taggable_id`,`taggable_type`),
  KEY `taggables_taggable_type_taggable_id_index` (`taggable_type`,`taggable_id`),
  CONSTRAINT `taggables_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` json NOT NULL,
  `slug` json NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `changelog` tinyint(1) NOT NULL DEFAULT '0',
  `order_column` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_socials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_socials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'sso',
  `provider_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` text COLLATE utf8mb4_unicode_ci,
  `refresh_token` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_socials_user_id_foreign` (`user_id`),
  CONSTRAINT `user_socials_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `notification_settings` json DEFAULT NULL,
  `per_page_setting` json DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_locale` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subscribed` tinyint(1) NOT NULL DEFAULT '1',
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `votes_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `votes_user_id_foreign` (`user_id`),
  CONSTRAINT `votes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2022_05_03_074729_create_projects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2022_05_03_074733_create_boards_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2022_05_03_074737_create_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2022_05_03_074741_create_votes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2022_05_03_075812_create_comments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2022_05_03_120409_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2022_05_03_121713_create_general_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2022_05_03_150014_update_items_table_change_votes_name',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2022_05_03_154723_update_boards_table_add_sort_order',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2022_05_06_084714_add_subscribed_to_votes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2022_05_06_102326_add_project_id_to_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2022_05_06_104233_add_visible_to_boards_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2022_05_08_123013_add_sort_items_by_to_boards_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2022_05_11_164916_add_item_user_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2022_05_14_084511_create_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2022_05_14_084512_add_event_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2022_05_14_084513_add_batch_uuid_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2022_05_23_064445_create_mentions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2022_05_23_165454_update_general',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2022_05_23_172413_update_general_favicon',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2022_05_23_180406_add_option_to_toggle_item_age',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2022_05_24_113229_add_slugs',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2022_05_26_064327_create_user_socials_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2022_05_27_060215_update_general_again',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2022_05_28_135244_add_block_robots_setting',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2022_05_31_122229_add_settings_for_required_toggles',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2022_06_05_223148_add_parent_id_to_comments_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2022_06_09_053413_add_columns_to_boards_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2022_06_13_142538_add_pin_to_items',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2022_06_17_053959_convert_roles_for_users',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2022_06_18_084743_add_private_column_to_items_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2022_06_19_172203_add_custom_scripts_setting',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2022_06_20_112935_add_private_column_to_comments_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2022_06_21_161821_add_inbox_workflow_setting',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2022_06_22_160414_add_toggle_for_voter_avatars_in_item_view',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2022_06_23_124636_add_private_column_to_projects_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2022_06_24_092158_create_jobs_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2022_06_24_095822_add_user_verified_setting',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2022_06_27_160827_create_changelog_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2022_06_27_233944_changelog_settings',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2022_06_30_100948_add_notify_subscribers_column_to_items_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2022_07_10_144807_add_icon_to_projects_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2022_07_26_035141_add_sort_order_to_projects_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2022_08_26_195053_add_per_page_setting_to_user_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2022_09_14_052900_create_notifications_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2022_09_30_195933_add_total_votes_to_comments_table',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2022_10_02_121026_create_tag_tables',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2022_10_12_063702_convert_current_notification_settings',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2022_10_18_104540_add_date_locale_to_users_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2022_10_18_112136_add_locale_to_users_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2022_10_18_114350_add_disable_file_uploads',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2022_10_18_173054_create_project_member_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2022_10_24_170113_add_changelog_to_tags',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2022_10_27_131258_add_excluded_words_setting',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2023_01_02_082337_add_profanity',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2023_01_27_002946_convert_default_boards_setting',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2023_02_16_095746_add_group_to_projects_table',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2023_03_28_182052_add_font_family',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2023_03_28_183942_add_logo',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2023_03_30_132918_add_repo_to_projects_table',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2023_03_30_135917_add_issue_to_items_table',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2023_03_30_164137_add_github',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2080_01_27_002946_convert_default_boards_setting',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2023_08_30_105938_fix_settings_table',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2023_10_30_145814_remove_project_icons_for_filament_v3',35);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2081_11_01_075103_convert_default_boards_again',36);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2024_01_09_181742_create_disable_user_setting',37);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2024_01_16_112758_add_changelog_vote_option',38);
