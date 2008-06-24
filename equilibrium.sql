-- Copyright 2008, St. Jude Children's Research Hospital.
-- Written by Dr. John Obenauer, john.obenauer@stjude.org.

-- This file is part of Equilibrium.  Equilibrium is free software:
-- you can redistribute it and/or modify it under the terms of the
-- GNU General Public License as published by the Free Software
-- Foundation, either version 2 of the License, or (at your option)
-- any later version.

-- Equilibrium is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.

-- You should have received a copy of the GNU General Public License
-- along with Equilibrium.  If not, see <http://www.gnu.org/licenses/>.

--
-- Equilibrium database schema
--

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `first_name` varchar(20) default NULL,
  `last_name` varchar(20) NOT NULL,
  `login` varchar(20) default NULL,
  `email` varchar(50) NOT NULL,
  `authentication` enum('LDAP','local') NOT NULL default 'local',
  `password` varchar(255) default NULL,
  `department_id` int(10) unsigned default NULL,
  `staff_flag` enum('Y','N') NOT NULL default 'N',
  `admin_priv` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO users (user_id, first_name, last_name, login, email, authentication, password, staff_flag, admin_priv) values (1, '', 'Administrator', 'admin', '' 'local', password(""), 'N', 'Y');

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `project_types`
--

DROP TABLE IF EXISTS `project_types`;
CREATE TABLE `project_types` (
  `project_type_id` int(10) unsigned NOT NULL auto_increment,
  `name` text NOT NULL,
  `description` text,
  `created_by` int(10) unsigned NULL,
  PRIMARY KEY  (`project_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- These will need to be defined by any group that uses Equilibrium
INSERT INTO project_types (name, description) values ('Client support', 'Performing tasks requested by clients.');
INSERT INTO project_types (name, description) values ('Training', 'Training staff members or clients, whether in a class setting, a seminar, or individually.');
INSERT INTO project_types (name, description) values ('Continuing education', 'Reading relevant articles or books, learning new skills, attending conferences or training sessions, or other work to maintain or improve job-related knowledge.');
INSERT INTO project_types (name, description) values ('Administrative tasks', 'Preparing annual reports, performance evaluations, budgets, or other administrative tasks required for the group or department.');
-- "Other" category is required to have project_type_id = 100
INSERT INTO project_types (project_type_id, name, description) values (100, 'Other', 'Any work not fitting into another category.');

--
-- Table structure for table `project_history`
--

DROP TABLE IF EXISTS `project_history`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `duties`
--

DROP TABLE IF EXISTS `duties`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `duty_types`
--

DROP TABLE IF EXISTS `duty_types`;
CREATE TABLE `duty_types` (
  `duty_type_id` int(10) unsigned NOT NULL auto_increment,
  `name` text NOT NULL,
  `description` text,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`duty_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- These will need to be defined by any group that uses Equilibrium
INSERT INTO duty_types (name, description) values ('Client support', 'Performing tasks requested by clients.');
INSERT INTO duty_types (name, description) values ('Training', 'Training staff members or clients, whether in a class setting, a seminar, or individually.');
INSERT INTO duty_types (name, description) values ('Continuing education', 'Reading relevant articles or books, learning new skills, attending conferences or training sessions, or other work to maintain or improve job-related knowledge.');
INSERT INTO duty_types (name, description) values ('Administrative tasks', 'Preparing annual reports, performance evaluations, budgets, or other administrative tasks required for the group or department.');
-- "Other" category is required to have duty_type_id = 100
INSERT INTO duty_types (duty_type_id, name, description) values (100, 'Other', 'Any work not fitting into another category.');

--
-- Table structure for table `duty_history`
--

DROP TABLE IF EXISTS `duty_history`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `client_id` int(10) unsigned NOT NULL auto_increment,
  `first_name` varchar(20) default NULL,
  `last_name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `directory` varchar(20) NOT NULL,
  `department_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `client_history`
--

DROP TABLE IF EXISTS `client_history`;
CREATE TABLE `client_history` (
  `client_history_id` int(10) unsigned NOT NULL auto_increment,
  `staff_id` int(10) unsigned default NULL,
  `client_id` int(10) unsigned default NULL,
  `project_id` int(10) unsigned NOT NULL default '0',
  `duty_id` int(10) unsigned NOT NULL default '0',
  `client_entered_date` date NOT NULL,
  `client_entered_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`client_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `department_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `todos`
--

DROP TABLE IF EXISTS `todos`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
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

--
-- Table structure for table `icon_usage`
--

DROP TABLE IF EXISTS `icon_usage`;
CREATE TABLE `icon_usage` (
  `icon_usage_id` int(10) unsigned NOT NULL auto_increment,
  `icon_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL default '0',
  `duty_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`icon_usage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

