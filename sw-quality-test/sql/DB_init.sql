/* =========================================================
   SW Quality Test Shop - MySQL Schema (all-in-one)
   - utf8mb4 / InnoDB
   - No FOREIGN KEY constraints (조인은 애플리케이션에서)
   - Users/Admins 분리, 포인트 결제 지원
   ========================================================= */

-- 안전 옵션(선택)
SET NAMES utf8mb4;
SET time_zone = '+09:00';
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- 0) 데이터베이스 생성
CREATE DATABASE IF NOT EXISTS `sw_quality_test_shop`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE `sw_quality_test_shop`;

-- 공통 기본값 매크로 느낌(주석용)
-- created_at  : DEFAULT CURRENT_TIMESTAMP
-- updated_at  : DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

/* =========================
   1) 계정 도메인
   ========================= */

-- 1-1. 일반 사용자 (포인트 보유)
CREATE TABLE IF NOT EXISTS `users` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`           VARCHAR(255)     NOT NULL,
  `password`        VARCHAR(255)     NOT NULL,
  `name`            VARCHAR(100)     NOT NULL,
  `phone`           VARCHAR(30)      DEFAULT NULL,
  `address`         VARCHAR(255)     DEFAULT NULL,

  `points`          INT UNSIGNED     NOT NULL DEFAULT 0,     -- ★ 포인트 잔액

  `status`          ENUM('active','blocked') NOT NULL DEFAULT 'active',
  `last_login_at`   DATETIME         DEFAULT NULL,
  `created_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`),
  KEY `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 1-2. 관리자
CREATE TABLE IF NOT EXISTS `admins` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`           VARCHAR(255)     NOT NULL,
  `password`        VARCHAR(255)     NOT NULL,
  `name`            VARCHAR(100)     NOT NULL,
  `role`            ENUM('super','manager','editor') NOT NULL DEFAULT 'manager',
  `status`          ENUM('active','blocked') NOT NULL DEFAULT 'active',
  `last_login_at`   DATETIME         DEFAULT NULL,
  `created_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_admins_email` (`email`),
  KEY `idx_admins_role` (`role`),
  KEY `idx_admins_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* =========================
   2) 상품/게시판 도메인
   ========================= */

-- 2-1. 상품
CREATE TABLE IF NOT EXISTS `products` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(200)    NOT NULL,
  `price`         INT UNSIGNED    NOT NULL,               -- KRW 정수
  `stock`         INT UNSIGNED    NOT NULL DEFAULT 0,
  `image_url`     VARCHAR(255)    DEFAULT NULL,
  `description`   TEXT            DEFAULT NULL,
  `is_active`     TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  KEY `idx_products_active` (`is_active`),
  KEY `idx_products_price`  (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2-2. 게시글
CREATE TABLE IF NOT EXISTS `board_posts` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`           VARCHAR(200)    NOT NULL,
  `content`         MEDIUMTEXT      NOT NULL,
  `author_user_id`  BIGINT UNSIGNED DEFAULT NULL,   -- FK 미사용
  `author_name`     VARCHAR(100)    DEFAULT NULL,   -- 게스트/닉네임 대응
  `views`           INT UNSIGNED    NOT NULL DEFAULT 0,
  `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  KEY `idx_board_author_user_id` (`author_user_id`),
  KEY `idx_board_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2-3. 댓글(선택)
CREATE TABLE IF NOT EXISTS `board_comments` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id`         BIGINT UNSIGNED NOT NULL,            -- FK 미사용
  `author_user_id`  BIGINT UNSIGNED DEFAULT NULL,        -- FK 미사용
  `author_name`     VARCHAR(100)    DEFAULT NULL,
  `content`         TEXT            NOT NULL,
  `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  KEY `idx_comments_post_id` (`post_id`),
  KEY `idx_comments_author_user_id` (`author_user_id`),
  KEY `idx_comments_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2-4. 게시글 첨부파일 (FK 미사용)
CREATE TABLE IF NOT EXISTS `board_post_files` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id`          BIGINT UNSIGNED NOT NULL,          -- board_posts.id (FK 미사용)

  `original_name`    VARCHAR(255)    NOT NULL,          -- 업로드 당시 파일명
  `stored_name`      VARCHAR(255)    NOT NULL,          -- 서버 저장 파일명(UUID 등)
  `stored_path`      VARCHAR(255)    NOT NULL,          -- 예: attachment/board/ 또는 uploads/board/
  `mime_type`        VARCHAR(100)    DEFAULT NULL,      -- 예: image/jpeg
  `file_size`        BIGINT UNSIGNED NOT NULL,          -- byte (큰 파일 대비)

  `download_count`   INT UNSIGNED    NOT NULL DEFAULT 0,
  `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  KEY `idx_bpf_post_id` (`post_id`),
  KEY `idx_bpf_post_id_created` (`post_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* =========================
   3) 장바구니/주문/포인트/로그
   ========================= */

-- 3-1. 장바구니 (필요 시 DB 사용)
CREATE TABLE IF NOT EXISTS `cart` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NOT NULL,   -- FK 미사용
  `product_id`  BIGINT UNSIGNED NOT NULL,   -- FK 미사용
  `quantity`    INT UNSIGNED    NOT NULL DEFAULT 1,
  `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  KEY `idx_cart_user_id` (`user_id`),
  KEY `idx_cart_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3-2. 주문
CREATE TABLE IF NOT EXISTS `orders` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number`     VARCHAR(40)     NOT NULL,           -- 예: VS-2025-000123
  `user_id`          BIGINT UNSIGNED NOT NULL,           -- FK 미사용

  `total_price`      INT UNSIGNED    NOT NULL,           -- 상품합계+배송비
  `shipping_fee`     INT UNSIGNED    NOT NULL DEFAULT 0,
  `paid_points`      INT UNSIGNED    NOT NULL DEFAULT 0, -- 사용 포인트
  `payment_method`   ENUM('CARD','BANK','POINTS','MIX') NOT NULL DEFAULT 'CARD',
  `status`           ENUM('PENDING','PAID','SHIPPING','COMPLETED','CANCELLED','REFUND') NOT NULL DEFAULT 'PENDING',

  `receiver_name`    VARCHAR(100)    NOT NULL,
  `receiver_phone`   VARCHAR(30)     NOT NULL,
  `receiver_address` VARCHAR(255)    NOT NULL,
  `delivery_request` VARCHAR(255)    DEFAULT NULL,

  `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_orders_order_number` (`order_number`),
  KEY `idx_orders_user_id` (`user_id`),
  KEY `idx_orders_status_created` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3-3. 주문 아이템(스냅샷)
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`      BIGINT UNSIGNED NOT NULL,        -- FK 미사용
  `product_id`    BIGINT UNSIGNED DEFAULT NULL,     -- 상품 삭제 대비 스냅샷
  `product_name`  VARCHAR(200)    NOT NULL,
  `option_info`   VARCHAR(200)    DEFAULT NULL,
  `quantity`      INT UNSIGNED    NOT NULL,
  `price`         INT UNSIGNED    NOT NULL,         -- 주문 당시 단가

  PRIMARY KEY (`id`),
  KEY `idx_order_items_order_id` (`order_id`),
  KEY `idx_order_items_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3-4. 포인트 이력 (증감 로그)
CREATE TABLE IF NOT EXISTS `point_history` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`        BIGINT UNSIGNED NOT NULL,        -- FK 미사용
  `type`           ENUM('EARN','SPEND','REFUND','ADJUST') NOT NULL,
  `delta`          INT              NOT NULL,       -- +적립 / -차감
  `balance_after`  INT UNSIGNED     NOT NULL,       -- 적용 후 잔액
  `ref_type`       ENUM('ORDER','ADMIN','ETC') DEFAULT 'ETC',
  `ref_id`         BIGINT UNSIGNED  DEFAULT NULL,   -- orders.id 등
  `memo`           VARCHAR(255)     DEFAULT NULL,
  `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  KEY `idx_points_user_id_created` (`user_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3-5. 시스템 로그 (감사 로그)
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `actor_type`  ENUM('USER','ADMIN','SYSTEM') NOT NULL,
  `actor_id`    BIGINT UNSIGNED  DEFAULT NULL,
  `action`      VARCHAR(100)     NOT NULL,
  `detail`      TEXT             DEFAULT NULL,
  `ip`          VARCHAR(45)      DEFAULT NULL,
  `ua`          VARCHAR(255)     DEFAULT NULL,
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  KEY `idx_logs_actor` (`actor_type`,`actor_id`),
  KEY `idx_logs_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* =========================================================
   (옵션) 초기 관리자 계정/더미 데이터 삽입 예시
   비밀번호는 반드시 PHP에서 password_hash()로 생성 후 삽입
   ========================================================= */
-- INSERT INTO `admins` (`email`,`password_hash`,`name`,`role`)
-- VALUES ('admin@example.com','$2y$10$hashhashhash...','관리자','super');

-- INSERT INTO `products` (`name`,`price`,`stock`,`image_url`,`description`)
-- VALUES ('무선 마우스',15000,100,'','테스트 상품입니다.');

-- INSERT INTO `users` (`email`,`password_hash`,`name`,`points`)
-- VALUES ('user@example.com','$2y$10$hashhashhash...','홍길동',50000);

-- 완료
SELECT '✅ Database & tables created.' AS status;