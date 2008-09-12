-- MySQL dump 10.11
--
-- Host: localhost    Database: equilibrium
-- ------------------------------------------------------
-- Server version	5.0.51a-3ubuntu5.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `assistants`
--

DROP TABLE IF EXISTS `assistants`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `assistants` (
  `assistant_id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned default NULL,
  `duty_id` int(10) unsigned default NULL,
  `staff_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`assistant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `assistants`
--

LOCK TABLES `assistants` WRITE;
/*!40000 ALTER TABLE `assistants` DISABLE KEYS */;
INSERT INTO `assistants` VALUES (1,0,1,0),(3,0,2,0),(4,0,3,0),(6,3,0,0);
/*!40000 ALTER TABLE `assistants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_history`
--

DROP TABLE IF EXISTS `client_history`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `client_history` (
  `client_history_id` int(10) unsigned NOT NULL auto_increment,
  `staff_id` int(10) unsigned default NULL,
  `client_id` int(10) unsigned default NULL,
  `project_id` int(10) unsigned NOT NULL default '0',
  `duty_id` int(10) unsigned NOT NULL default '0',
  `client_entered_date` date NOT NULL,
  `client_entered_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`client_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `client_history`
--

LOCK TABLES `client_history` WRITE;
/*!40000 ALTER TABLE `client_history` DISABLE KEYS */;
INSERT INTO `client_history` VALUES (1,2,0,0,1,'2008-09-05','2008-09-05 21:17:57'),(2,2,1,1,0,'2008-09-05','2008-09-05 22:05:24'),(3,2,0,0,2,'2008-09-05','2008-09-05 22:10:36'),(4,2,0,0,3,'2008-09-05','2008-09-05 22:16:09'),(5,2,0,2,0,'2008-09-05','2008-09-05 22:37:25'),(6,2,0,3,0,'2008-09-05','2008-09-05 22:42:25');
/*!40000 ALTER TABLE `client_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `clients` (
  `client_id` int(10) unsigned NOT NULL auto_increment,
  `first_name` varchar(20) default NULL,
  `last_name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `directory` varchar(20) NOT NULL,
  `department_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'Andre','Sali','sali@salilab.org','',1),(2,'Derek','Persons','derek.persons@stjude.org','',2),(3,'Derek','Persons','derek.persons@stjude.org','',2);
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `comments` (
  `comment_id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL default '0',
  `duty_id` int(10) unsigned NOT NULL default '0',
  `comment_text` text NOT NULL,
  `visibility` enum('Private','Public') NOT NULL default 'Public',
  `submitter_id` int(10) unsigned NOT NULL,
  `submit_date` date NOT NULL,
  `submit_time` timestamp NULL default NULL,
  PRIMARY KEY  (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,0,2,'I reviewed a paper for PLoS Pathogens claiming to find new clades in a phylogenetic tree of Staphylococcus genes.  The previously known clades show up clearly in the tree, but the two new clades rely on a small number of samples that are not very homogeneous.  They also did not deposit their new sequences in GenBank in time for review, so I couldn\\\'t check the sequences myself.','Public',2,'2008-09-05','2008-09-05 22:14:16'),(2,0,2,'I reviewed a paper for PNAS providing experimental support for a hypothesized virulence marker in influenza.  Ferrets and mice infected with the proposed marker showed higher morbidity and mortality than the control animals.  The analysis was very straightforward.  I recommended it for publication with only minor revisions.','Public',2,'2008-09-05','2008-09-05 22:32:17'),(3,0,0,'We had a meeting today to discuss what sequence analysis software we should provide and support institution-wide.  We narrowed the choices down to Lasergene and CLC Biobench.  CLC Biobench has more features, but its pricing is exorbitant.  Lasergene is probably sufficient for most of our needs, and the Bioinformatics group can provide customized work in cases where Lasergene is unsuitable.','Public',2,'2008-09-05','2008-09-05 22:53:58');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `departments` (
  `department_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Biopharmaceutical Sciences'),(2,'Hematology');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `duties`
--

DROP TABLE IF EXISTS `duties`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `duties` (
  `duty_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `duty_type_id` int(10) unsigned default NULL,
  `description` text NOT NULL,
  `staff_assigned` int(10) unsigned NOT NULL default '0',
  `client_id` int(10) unsigned default '0',
  `contact` varchar(50) default NULL,
  `date_entered` date default NULL,
  `status` enum('Active','Inactive') NOT NULL default 'Active',
  `icon_id` int(10) unsigned NOT NULL,
  `order_number` int(10) unsigned default NULL,
  `visibility` enum('Private','Public') NOT NULL default 'Public',
  PRIMARY KEY  (`duty_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `duties`
--

LOCK TABLES `duties` WRITE;
/*!40000 ALTER TABLE `duties` DISABLE KEYS */;
INSERT INTO `duties` VALUES (1,'Journal club',3,'Present and evaluate the results of a recent scientific paper.',2,0,'','2008-09-05','Active',25376,NULL,'Public'),(2,'Review manuscripts',4,'Review submitted manuscripts and send evaluations to journal editors.',2,0,'','2008-09-05','Active',11044,NULL,'Public'),(3,'Performance evaluations',4,'Fill out annual performance evaluations and meet with staff to discuss them.',2,0,'','2008-09-05','Active',1308,NULL,'Public');
/*!40000 ALTER TABLE `duties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `duty_history`
--

DROP TABLE IF EXISTS `duty_history`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `duty_history` (
  `duty_history_id` int(10) unsigned NOT NULL auto_increment,
  `duty_id` int(10) unsigned NOT NULL,
  `status` enum('Active','Inactive') NOT NULL default 'Active',
  `date_entered` date default NULL,
  `date_started` date default NULL,
  `date_completed` date default NULL,
  `modification_date` date NOT NULL,
  `modification_time` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`duty_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `duty_history`
--

LOCK TABLES `duty_history` WRITE;
/*!40000 ALTER TABLE `duty_history` DISABLE KEYS */;
INSERT INTO `duty_history` VALUES (1,1,'Active',NULL,NULL,NULL,'2008-09-05','2008-09-05 21:17:57'),(2,2,'Active',NULL,NULL,NULL,'2008-09-05','2008-09-05 22:10:36'),(3,3,'Active',NULL,NULL,NULL,'2008-09-05','2008-09-05 22:16:09');
/*!40000 ALTER TABLE `duty_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `duty_types`
--

DROP TABLE IF EXISTS `duty_types`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `duty_types` (
  `duty_type_id` int(10) unsigned NOT NULL auto_increment,
  `name` text NOT NULL,
  `description` text,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`duty_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `duty_types`
--

LOCK TABLES `duty_types` WRITE;
/*!40000 ALTER TABLE `duty_types` DISABLE KEYS */;
INSERT INTO `duty_types` VALUES (1,'Client support','Performing tasks requested by clients.',0),(2,'Training','Training staff members or clients, whether in a class setting, a seminar, or individually.',0),(3,'Continuing education','Reading relevant articles or books, learning new skills, attending conferences or training sessions, or other work to maintain or improve job-related knowledge.',0),(4,'Administrative tasks','Preparing annual reports, performance evaluations, budgets, or other administrative tasks required for the group or department.',0),(100,'Other','Any work not fitting into another category.',0);
/*!40000 ALTER TABLE `duty_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `files` (
  `file_id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL default '0',
  `duty_id` int(10) unsigned NOT NULL default '0',
  `file_name` varchar(50) NOT NULL,
  `file_path` varchar(200) default NULL,
  `file_type` enum('Report','Program','Protocol') default NULL,
  `description` varchar(255) default NULL,
  `upload_date` date NOT NULL default '0000-00-00',
  `upload_time` timestamp NOT NULL default '0000-00-00 00:00:00',
  `uploaded_by` int(10) unsigned default NULL,
  PRIMARY KEY  (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `icon_usage`
--

DROP TABLE IF EXISTS `icon_usage`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `icon_usage` (
  `icon_usage_id` int(10) unsigned NOT NULL auto_increment,
  `icon_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL default '0',
  `duty_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`icon_usage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `icon_usage`
--

LOCK TABLES `icon_usage` WRITE;
/*!40000 ALTER TABLE `icon_usage` DISABLE KEYS */;
INSERT INTO `icon_usage` VALUES (1,25376,0,1),(2,29832,1,0),(3,11044,0,2),(4,1308,0,3),(5,7806,2,0),(6,19950,3,0);
/*!40000 ALTER TABLE `icon_usage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_history`
--

DROP TABLE IF EXISTS `project_history`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `project_history` (
  `project_history_id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL default '0',
  `status` enum('Proposed','Pending','Active','Paused','Aborted','Completed') NOT NULL default 'Proposed',
  `request_date` date default NULL,
  `est_start_date` date default NULL,
  `est_stop_date` date default NULL,
  `modification_date` date NOT NULL,
  `modification_time` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`project_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `project_history`
--

LOCK TABLES `project_history` WRITE;
/*!40000 ALTER TABLE `project_history` DISABLE KEYS */;
INSERT INTO `project_history` VALUES (1,1,'Pending',NULL,NULL,NULL,'2008-09-05','2008-09-05 22:05:24'),(2,1,'Active',NULL,NULL,NULL,'2008-09-05','2008-09-05 22:09:25'),(3,2,'Active',NULL,NULL,NULL,'2008-09-05','2008-09-05 22:37:25'),(4,3,'Pending',NULL,NULL,NULL,'2008-09-05','2008-09-05 22:42:25');
/*!40000 ALTER TABLE `project_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_types`
--

DROP TABLE IF EXISTS `project_types`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `project_types` (
  `project_type_id` int(10) unsigned NOT NULL auto_increment,
  `name` text NOT NULL,
  `description` text,
  `created_by` int(10) unsigned default NULL,
  PRIMARY KEY  (`project_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `project_types`
--

LOCK TABLES `project_types` WRITE;
/*!40000 ALTER TABLE `project_types` DISABLE KEYS */;
INSERT INTO `project_types` VALUES (1,'Client support','Performing tasks requested by clients.',NULL),(2,'Training','Training staff members or clients, whether in a class setting, a seminar, or individually.',NULL),(3,'Continuing education','Reading relevant articles or books, learning new skills, attending conferences or training sessions, or other work to maintain or improve job-related knowledge.',NULL),(4,'Administrative tasks','Preparing annual reports, performance evaluations, budgets, or other administrative tasks required for the group or department.',NULL),(100,'Other','Any work not fitting into another category.',NULL);
/*!40000 ALTER TABLE `project_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `projects` (
  `project_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `project_type_id` int(10) unsigned default NULL,
  `description` text NOT NULL,
  `staff_assigned` int(10) unsigned NOT NULL default '0',
  `client_id` int(10) unsigned default '0',
  `contact` varchar(50) default NULL,
  `date_entered` date default NULL,
  `date_started` date default NULL,
  `date_completed` date default NULL,
  `status` enum('Proposed','Pending','Active','Suspended','Aborted','Completed') NOT NULL default 'Proposed',
  `icon_id` int(10) unsigned NOT NULL,
  `order_number` int(10) unsigned default NULL,
  `visibility` enum('Private','Public') NOT NULL default 'Public',
  PRIMARY KEY  (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,'Calculate phi and psi angles for protein structures.',1,'A new faculty member asked for a program to read in PDB files and use the coordinates to calculate phi and psi angles along the protein backbone.',2,1,'','2008-09-05','2008-09-05',NULL,'Active',29832,NULL,'Public'),(2,'Create pipeline for mapping viral insertions',1,'An investigator has sequenced mouse genomic regions representing where a retroviral vector has inserted a therapeutic globin gene.  He wants an automated way to reliably identify the correct genomic insertion site for hundreds of these sequences.',2,3,'','2008-09-05','2008-09-05','0000-00-00','Active',7806,NULL,'Public'),(3,'Develop method for assigning genotypes automatically and objectively',1,'Genotypes are often defined visually by seeing anomalous parts of a phylogenetic tree.  As a result, some characterized genotypes are much more similar than others.  For such situations, it would be preferable to identify genotypes based on objective criteria, such as a sequence identity cutoff, or a branch length difference.',2,0,'','2008-09-05',NULL,NULL,'Pending',19950,NULL,'Public');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `todos`
--

DROP TABLE IF EXISTS `todos`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `todos` (
  `todo_id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL default '0',
  `duty_id` int(10) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  `staff_assigned` int(10) unsigned default NULL,
  `order_number` int(10) unsigned default NULL,
  `completed` enum('N','Y') default 'N',
  `completed_date` date default NULL,
  `completed_time` timestamp NULL default NULL,
  `schedule_date` date default NULL,
  `schedule_order` int(10) unsigned default NULL,
  `project_order` int(10) unsigned default NULL,
  `duty_order` int(10) unsigned default NULL,
  `priority` enum('High','Low') default 'High',
  `visibility` enum('Private','Public') NOT NULL default 'Public',
  PRIMARY KEY  (`todo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `todos`
--

LOCK TABLES `todos` WRITE;
/*!40000 ALTER TABLE `todos` DISABLE KEYS */;
INSERT INTO `todos` VALUES (2,0,1,'Read Shinya Yamanaka\'s paper on induced pluripotent stem cells and make slides.',2,NULL,'N',NULL,NULL,NULL,NULL,NULL,1,'High','Public'),(3,1,0,'Look up formula for calculating dihedral angles.',2,NULL,'N',NULL,NULL,NULL,NULL,1,NULL,'High','Public'),(4,1,0,'Read a test PDB file in using Perl and find all the CA, CO, NH, and O atomic positions.',2,NULL,'N',NULL,NULL,NULL,NULL,2,NULL,'High','Public'),(5,1,0,'Plot calculated angles on a Ramachandran plot.',2,NULL,'N',NULL,NULL,NULL,NULL,4,NULL,'High','Public'),(6,1,0,'Check that the sign of the dihedral angles is correct.',2,NULL,'N',NULL,NULL,NULL,NULL,3,NULL,'High','Public'),(7,0,3,'Nominate Chunxu Qu for the CIO Vision Award because of her work solving the copy number analysis problem.',2,NULL,'N',NULL,NULL,NULL,NULL,NULL,1,'High','Public'),(8,2,0,'Download mouse genome from UCSC.',2,NULL,'N',NULL,NULL,NULL,NULL,1,NULL,'High','Public'),(9,2,0,'Install BLAST and test which settings are most efficient for mapping these sequence fragments.',2,NULL,'N',NULL,NULL,NULL,NULL,2,NULL,'High','Public'),(10,2,0,'Compare genomic positions found with gene exon and intron locations.',2,NULL,'N',NULL,NULL,NULL,NULL,3,NULL,'High','Public');
/*!40000 ALTER TABLE `todos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `first_name` varchar(20) default NULL,
  `last_name` varchar(20) NOT NULL,
  `login` varchar(20) default NULL,
  `email` varchar(50) default NULL,
  `authentication` enum('LDAP','local') NOT NULL default 'local',
  `password` varchar(255) default NULL,
  `department_id` int(10) unsigned default NULL,
  `staff_flag` enum('Y','N') NOT NULL default 'N',
  `admin_priv` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'','Administrator','admin','','local','',NULL,'N','Y'),(2,'John','Obenauer','obenauer','john.obenauer@stjude.org','local','*852045D766B28A2D42A2F49D4C7C625802F1661C',NULL,'Y','Y');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-09-05 22:56:12
