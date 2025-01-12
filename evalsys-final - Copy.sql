-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.0 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.7.0.6850
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for evaluation_system
CREATE DATABASE IF NOT EXISTS `evaluation_system`;
USE `evaluation_system`;

-- Dumping structure for table evaluation_system.admin
CREATE TABLE IF NOT EXISTS `admin` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT (now()),
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status` tinyint DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.course
CREATE TABLE IF NOT EXISTS `course` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `acronym` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.edp
CREATE TABLE IF NOT EXISTS `edp` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `edp_code` bigint DEFAULT NULL,
  `year_level` int NOT NULL,
  `year` int DEFAULT '2024',
  `semester` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `subject_id` bigint NOT NULL,
  `teacher_id` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `edp_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`),
  CONSTRAINT `edp_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.history
CREATE TABLE IF NOT EXISTS `history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `message` varchar(2048) NOT NULL,
  `user_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.staff
CREATE TABLE IF NOT EXISTS `staff` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.staff_feedback
CREATE TABLE IF NOT EXISTS `staff_feedback` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `feedback` varchar(2048) NOT NULL,
  `year` bigint NOT NULL,
  `semester` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `eval_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `eval_id` (`eval_id`),
  CONSTRAINT `staff_feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `staff_feedback_ibfk_2` FOREIGN KEY (`eval_id`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.staff_form
CREATE TABLE IF NOT EXISTS `staff_form` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `point` bigint NOT NULL,
  `year` bigint NOT NULL,
  `semester` bigint NOT NULL,
  `question_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `eval_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `eval_id` (`eval_id`),
  KEY `FK_staff_form_staff_form_question` (`question_id`),
  CONSTRAINT `FK_staff_form_staff_form_question` FOREIGN KEY (`question_id`) REFERENCES `staff_form_question` (`id`),
  CONSTRAINT `staff_form_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `staff_form_ibfk_3` FOREIGN KEY (`eval_id`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.staff_form_question
CREATE TABLE IF NOT EXISTS `staff_form_question` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `question` varchar(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.student_form
CREATE TABLE IF NOT EXISTS `student_form` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `image` text NOT NULL,
  `year` int NOT NULL,
  `semester` int NOT NULL,
  `status` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `user_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT (now()),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `FK_student_form_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.study_load
CREATE TABLE IF NOT EXISTS `study_load` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `edp_id` bigint NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `edp_id` (`edp_id`),
  CONSTRAINT `study_load_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `study_load_ibfk_2` FOREIGN KEY (`edp_id`) REFERENCES `edp` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.subject
CREATE TABLE IF NOT EXISTS `subject` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.teacher
CREATE TABLE IF NOT EXISTS `teacher` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `course_id` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.teacher_feedback
CREATE TABLE IF NOT EXISTS `teacher_feedback` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `feedback` varchar(2048) NOT NULL,
  `year` bigint NOT NULL,
  `semester` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `eval_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `eval_id` (`eval_id`),
  CONSTRAINT `teacher_feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `teacher_feedback_ibfk_2` FOREIGN KEY (`eval_id`) REFERENCES `edp` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.teacher_form
CREATE TABLE IF NOT EXISTS `teacher_form` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `point` bigint NOT NULL,
  `year` bigint NOT NULL,
  `semester` bigint NOT NULL,
  `question_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `eval_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `question_id` (`question_id`),
  KEY `eval_id` (`eval_id`),
  CONSTRAINT `teacher_form_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `teacher_form_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `teacher_form_question` (`id`),
  CONSTRAINT `teacher_form_ibfk_3` FOREIGN KEY (`eval_id`) REFERENCES `edp` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.teacher_form_question
CREATE TABLE IF NOT EXISTS `teacher_form_question` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `question` varchar(1024) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `email` varchar(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `school_id` bigint DEFAULT NULL,
  `fname` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `mname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `is_working` tinyint(1) DEFAULT (0),
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `type` enum('Student','Dean') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Student',
  `last_login` timestamp NULL DEFAULT (now()),
  `course_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`,`school_id`) USING BTREE,
  KEY `course_id` (`course_id`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.working_student_feedback
CREATE TABLE IF NOT EXISTS `working_student_feedback` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `feedback` varchar(2048) NOT NULL,
  `year` bigint NOT NULL,
  `semester` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `eval_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `eval_id` (`eval_id`),
  CONSTRAINT `working_student_feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `working_student_feedback_ibfk_2` FOREIGN KEY (`eval_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.working_student_form
CREATE TABLE IF NOT EXISTS `working_student_form` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `point` bigint NOT NULL,
  `year` bigint NOT NULL,
  `semester` bigint NOT NULL,
  `question_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `eval_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `eval_id` (`eval_id`),
  KEY `FK_working_student_form_working_student_form_question` (`question_id`),
  CONSTRAINT `FK_working_student_form_working_student_form_question` FOREIGN KEY (`question_id`) REFERENCES `working_student_form_question` (`id`),
  CONSTRAINT `working_student_form_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `working_student_form_ibfk_3` FOREIGN KEY (`eval_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table evaluation_system.working_student_form_question
CREATE TABLE IF NOT EXISTS `working_student_form_question` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `question` varchar(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Dumping data for table evaluation_system.admin: ~1 rows (approximately)
INSERT INTO `admin` (`id`, `fname`, `mname`, `lname`, `email`, `last_login`, `password`, `status`) VALUES
	(100001, 'admin', '', 'user', 'evalsys_admin@gmail.com', '2024-12-22 05:39:59', '$2y$12$Fr2jy3OM0iB144c4FRo/Depb7FXmUM8Nni6NCoTpO9QDBMzkf.qeG', 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
