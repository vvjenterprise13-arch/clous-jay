-- ============================================================
-- Railway MySQL ma aa SQL run karo (ekaj vaar)
-- php_sessions table banave che sessions store karva mate
-- ============================================================

CREATE TABLE IF NOT EXISTS `php_sessions` (
  `session_id` VARCHAR(128) NOT NULL,
  `data`       MEDIUMTEXT   NOT NULL,
  `expires`    DATETIME     NOT NULL,
  PRIMARY KEY (`session_id`),
  INDEX `idx_expires` (`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
