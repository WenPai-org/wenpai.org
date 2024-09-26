# WenPai.org 平台源码

这是一个站群平台，包含以下子平台。

| 网址                               | 描述     |
|----------------------------------|--------|
| https://wenpai.org               | 首页     |
| https://wenpai.org/support       | 支持论坛   |
| https://wenpai.org/themes        | 主题目录   |
| https://wenpai.org/plugins       | 插件目录   |
| https://translate.wenpai.org     | 翻译平台   |
| https://wenpai.org/documentation | 文档平台   |
| https://wenpai.org/news          | 新闻博客   |
| https://api.wenpai.org           | 对外 API |
| https://make.wenpai.org          | 参与贡献   |

本项目基于 GPL v3 协议开源，任何人都可以自由的复制、分发、重构本项目的所有代码，并将其用于任何目的，而不用承担任何法律风险。

## 运行环境

- [耗子面板](https://github.com/TheTNB/panel)
- PHP 8.3 及以上版本
- MySQL 5.7 及以上版本

## 依赖的外部环境

1. WP-CLI - 大量功能依赖此程序处理
2. Cavalcade - 用于处理所有 `Cron` 队列
3. ElasticSearch - 用于实现翻译记忆库及增强平台的搜索功能
4. gettext - 用于翻译平台生成 `mo` 文件
5. subversion - 用于应用市场爬虫获取更新记录
6. openjdk-17 - 机器翻译填充功能的术语表匹配机制需要对单词判断词性，该功能依赖一个 Java 库

## 特殊信息

平台使用了一些付费专业版插件，这部分插件我们无法提供，请自行购买，列表如下：

| slug                         | 名称                           |
|------------------------------|------------------------------|
| better-search-replace-pro    | Better Search Replace Pro    |
| duplicator-pro               | Duplicator Pro               |
| image-upload-for-bbpress-pro | Image Upload for bbPress Pro |
| object-cache-pro             | Object Cache Pro             |
| wp-all-export-pro            | WP All Export Pro            |
| powder-patterns              | Powder Patterns              |
| wp-oauth-server              | WP OAuth Server - Pro        |
| ultimate-branding            | Branda Pro                   |

由于一些限制，要求 GlotPress 中必须存在一些特殊 ID 的项目，具体可翻阅源码查找。

其余特殊配置请参考 `wp-config-sample.php` 文件。

翻译平台需要一个 `language_packs` 表用于存储语言包信息，其结构如下：

```sql
CREATE TABLE `language_packs`
(
    `id`            bigint     NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `language`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `type`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `type_raw`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `domain`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `version`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `active`        tinyint(1) NOT NULL                                           DEFAULT 0,
    `updated`       timestamp  NULL                                               DEFAULT NULL,
    `date_added`    timestamp  NULL                                               DEFAULT NULL,
    `date_modified` timestamp  NULL                                               DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
CREATE INDEX `language` ON `language_packs` (`language`);
CREATE INDEX `type` ON `language_packs` (`type`);
CREATE INDEX `type_raw` ON `language_packs` (`type_raw`);
CREATE INDEX `domain` ON `language_packs` (`domain`);
```

plat-gp-custom-stats 插件中也存在一些建表 SQL 需要运行。

## SQL

下面是一些日常可能会用到的 SQL 语句。

### 翻译平台清除无效的数据

```sql
DELETE
FROM wp_7_gp_projects
WHERE name = "";
DELETE
FROM wp_7_gp_meta
WHERE object_id NOT IN (SELECT id FROM wp_7_gp_projects);
DELETE
FROM wp_7_gp_projects
WHERE parent_project_id NOT IN (SELECT id
                                FROM (SELECT id FROM wp_7_gp_projects) AS temp);
DELETE
FROM wp_7_gp_translations
WHERE original_id NOT IN (SELECT id FROM wp_7_gp_originals);
DELETE
FROM wp_7_gp_originals
WHERE project_id NOT IN (SELECT id FROM wp_7_gp_projects);
```

### Cavalcade 将失败的任务重新加入队列

```sql
UPDATE wp_cavalcade_jobs
SET status = 'waiting'
WHERE status = 'failed';
```

## 常用命令

### 下面是一些可能会使用到的 CLI 命令。

```bash
wp --allow-root --url=wenpai.org/plugins platform wporg_plugins_update force_run
wp --allow-root --url=wenpai.org/themes platform wporg_themes_update force_run

wp --allow-root --url=translate.wenpai.org platform translate_import import --type=plugins --slug=woocommerce
wp --allow-root --url=translate.wenpai.org platform translate_import_release release --version=dev --display_version=dev --old_version=dev
wp --allow-root --url=translate.wenpai.org platform translate_pack all
wp --allow-root --url=translate.wenpai.org platform translate_memory clear
wp --allow-root --url=translate.wenpai.org platform translate_memory sync
wp --allow-root --url=translate.wenpai.org platform translate_import sync_all_product

wp --allow-root --url=wenpai.org/documentation platform helphub_import sync_all
```

## 技术支持

如果你在分叉过程中遇到问题可以在[文派支持论坛](https://wenpai.org/support)发帖交流，但我们仅能提供架构及程序流程咨询，如需定制开发请自行聘请工程师。
