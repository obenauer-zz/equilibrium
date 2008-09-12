--
-- Table structure for table `assistants`
--

DROP TABLE IF EXISTS `assistants`;
CREATE TABLE `assistants` (
  `assistant_id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned default NULL,
  `duty_id` int(10) unsigned default NULL,
  `staff_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`assistant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Make 'email' in users table "NULL"