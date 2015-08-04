-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 04, 2015 at 03:10 PM
-- Server version: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bitnami_moodle`
--

-- --------------------------------------------------------

--
-- Table structure for table `mdl_annotation_annotation`
--

CREATE TABLE IF NOT EXISTS `mdl_annotation_annotation` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `timecreated` int(11) NOT NULL,
  `annotation` text,
  `quote` text,
  `highlights` text NOT NULL,
  `ranges` text NOT NULL,
  `tags` text
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mdl_annotation_annotation`
--

INSERT INTO `mdl_annotation_annotation` (`id`, `userid`, `url`, `timecreated`, `annotation`, `quote`, `highlights`, `ranges`, `tags`) VALUES
(49, 3, 'http://localhost/moodle/mod/annotation/view.php?id=86', 1438469660, 'Created by a student', 'elit esse\ncillum dolore eu fug', '[{"jQuery111202184865882154554":"44"}]', '[{"start":"","startOffset":"305","end":"","endOffset":"335"}]', NULL),
(51, 2, 'http://localhost/moodle/mod/annotation/view.php?id=95', 1438627986, 'used by the browser to identify the markup language version', '&lt;!DOCTYPE html&gt;', '[{"jQuery1112015293302573263645":"44"},{"jQuery1112015293302573263645":"45"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[1]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[1]","endOffset":"15"}]', NULL),
(52, 2, 'http://localhost/moodle/mod/annotation/view.php?id=95', 1438628008, 'Not visible in a web browser', '&lt;head&gt;\n		&lt;title&gt;Basic Title&lt;/title&gt;\n		&lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; href=&quot;styles.css&quot;&gt;\n	&lt;/head&gt;', '[{"jQuery1112015293302573263645":"52"},{"jQuery1112015293302573263645":"53"},{"jQuery1112015293302573263645":"54"},{"jQuery1112015293302573263645":"55"},{"jQuery1112015293302573263645":"56"},{"jQuery1112015293302573263645":"57"},{"jQuery1112015293302573263645":"58"},{"jQuery1112015293302573263645":"59"},{"jQuery1112015293302573263645":"60"},{"jQuery1112015293302573263645":"61"},{"jQuery1112015293302573263645":"62"},{"jQuery1112015293302573263645":"63"},{"jQuery1112015293302573263645":"64"},{"jQuery1112015293302573263645":"65"},{"jQuery1112015293302573263645":"66"},{"jQuery1112015293302573263645":"67"},{"jQuery1112015293302573263645":"68"},{"jQuery1112015293302573263645":"69"},{"jQuery1112015293302573263645":"70"},{"jQuery1112015293302573263645":"71"},{"jQuery1112015293302573263645":"72"},{"jQuery1112015293302573263645":"73"},{"jQuery1112015293302573263645":"74"},{"jQuery1112015293302573263645":"75"},{"jQuery1112015293302573263645":"76"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[3]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[7]","endOffset":"7"}]', NULL),
(54, 2, 'http://localhost/moodle/mod/annotation/view.php?id=95', 1438628074, 'Specifies a CSS stylesheet to use', '&lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; href=&quot;styles.css&quot;&gt;', '[{"jQuery111209723373022861779":"71"},{"jQuery111209723373022861779":"72"},{"jQuery111209723373022861779":"73"},{"jQuery111209723373022861779":"74"},{"jQuery111209723373022861779":"75"},{"jQuery111209723373022861779":"76"},{"jQuery111209723373022861779":"77"},{"jQuery111209723373022861779":"78"},{"jQuery111209723373022861779":"79"},{"jQuery111209723373022861779":"80"},{"jQuery111209723373022861779":"81"},{"jQuery111209723373022861779":"82"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[7]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[7]","endOffset":"57"}]', NULL),
(55, 2, 'http://localhost/moodle/mod/annotation/view.php?id=95', 1438628088, 'heading one', '&lt;h1&gt;', '[{"jQuery111209723373022861779":"89"},{"jQuery111209723373022861779":"90"},{"jQuery111209723373022861779":"91"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[10]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[10]","endOffset":"4"}]', NULL),
(56, 2, 'http://localhost/moodle/mod/annotation/view.php?id=95', 1438628096, 'heading two', '&lt;h2&gt;', '[{"jQuery111209723373022861779":"88"},{"jQuery111209723373022861779":"95"},{"jQuery111209723373022861779":"96"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[12]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[12]","endOffset":"4"}]', NULL),
(57, 2, 'http://localhost/moodle/mod/annotation/view.php?id=95', 1438628107, 'paragraph tag', '&lt;p&gt;', '[{"jQuery111209723373022861779":"100"},{"jQuery111209723373022861779":"101"},{"jQuery111209723373022861779":"102"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[14]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[14]","endOffset":"3"}]', NULL),
(58, 2, 'http://localhost/moodle/mod/annotation/view.php?id=95', 1438628115, 'closing of body tag', '&lt;/body&gt;', '[{"jQuery111209723373022861779":"106"},{"jQuery111209723373022861779":"107"},{"jQuery111209723373022861779":"108"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[16]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[16]","endOffset":"7"}]', NULL),
(59, 2, 'http://localhost/moodle/mod/annotation/view.php?id=88', 1438689460, 'supports one data segment and one code segment', '.model small', '[{"jQuery111208229939839802682":"44"},{"jQuery111208229939839802682":"45"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[1]","startOffset":"0","end":"\\/pre[1]\\/code[1]","endOffset":"12"}]', NULL),
(60, 2, 'http://localhost/moodle/mod/annotation/view.php?id=88', 1438691317, 'The message to be displayed [edited]', '''Hello world!$''', '[{"jQuery111208229939839802682":"52"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[7]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[7]","endOffset":"15"}]', NULL),
(61, 2, 'http://localhost/moodle/mod/annotation/view.php?id=88', 1438629036, 'Allocate a 256-byte stack segment', '.stack 100h', '[{"jQuery111208229939839802682":"56"},{"jQuery111208229939839802682":"57"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[3]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[4]","endOffset":"4"}]', NULL),
(62, 2, 'http://localhost/moodle/mod/annotation/view.php?id=88', 1438691467, '100 hex = 256 decimal [overlap test][edited]', '100h', '[{"jQuery111208229939839802682":"61"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[4]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[4]","endOffset":"4"}]', NULL),
(65, 2, 'http://localhost/moodle/mod/annotation/view.php?id=86', 1438693398, 'Test overlap annotation [edited]', 'ore eu fugiat nulla paria', '[{"jQuery111209095682892948389":"51"},{"jQuery111209095682892948389":"52"}]', '[{"start":"","startOffset":"325","end":"","endOffset":"350"}]', NULL),
(66, 2, 'http://localhost/moodle/mod/annotation/view.php?id=86', 1438693391, 'Created by a teacher [edited]', 'daffoiwefj ewiTest Lorem ipsu', '[{"jQuery1112005784279038198292":"54"}]', '[{"start":"","startOffset":"0","end":"","endOffset":"29"}]', NULL),
(67, 2, 'http://localhost/moodle/mod/annotation/view.php?id=92', 1438678569, 'using the namespace fibonacci', 'namespace fibonacci', '[{"jQuery1112002522643725387752":"44"},{"jQuery1112002522643725387752":"45"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[6]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[7]","endOffset":"9"}]', NULL),
(68, 2, 'http://localhost/moodle/mod/annotation/view.php?id=92', 1438678578, 'defaults to public class', 'class', '[{"jQuery1112002522643725387752":"51"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[8]","startOffset":"0","end":"\\/pre[1]\\/code[1]","endOffset":"171"}]', NULL),
(69, 2, 'http://localhost/moodle/mod/annotation/view.php?id=92', 1438678586, 'name of class', 'Program', '[{"jQuery1112002522643725387752":"56"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[9]","startOffset":"0","end":"\\/pre[1]\\/code[1]\\/span[9]","endOffset":"7"}]', NULL),
(70, 2, 'http://localhost/moodle/mod/annotation/view.php?id=86', 1438684154, 'This is an annotation', 'sed do eiusmod\ntempor i', '[{"jQuery111206945124769117683":"52"},{"jQuery111206945124769117683":"53"},{"jQuery111206945124769117683":"51"},{"jQuery111206945124769117683":"50"},{"jQuery111206945124769117683":"49"}]', '[{"start":"","startOffset":"77","end":"","endOffset":"100"}]', NULL),
(71, 2, 'http://localhost/moodle/mod/annotation/view.php?id=88', 1438688443, 'end', 'end', '[{"jQuery111206547666140832007":"51"}]', '[{"start":"\\/pre[1]\\/code[1]","startOffset":"181","end":"\\/pre[1]\\/code[1]","endOffset":"185"}]', NULL),
(72, 2, 'http://localhost/moodle/mod/annotation/view.php?id=88', 1438690110, 'data section', 'data', '[{"jQuery111208397274068556726":"54"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[5]","startOffset":"1","end":"\\/pre[1]\\/code[1]\\/span[5]","endOffset":"5"}]', NULL),
(73, 3, 'http://localhost/moodle/mod/annotation/view.php?id=88', 1438692002, 'this is the code segment', 'code', '[{"jQuery11120520333755062893":"54"}]', '[{"start":"\\/pre[1]\\/code[1]\\/span[8]","startOffset":"1","end":"\\/pre[1]\\/code[1]\\/span[8]","endOffset":"5"}]', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mdl_annotation_annotation`
--
ALTER TABLE `mdl_annotation_annotation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mdl_annotation_annotation`
--
ALTER TABLE `mdl_annotation_annotation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=74;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
