<?php die('File not found !')?>
define("DB_EDOCUMENT", PREFIX."_edocument");
define("DB_EDOCUMENT_DOWNLOAD", PREFIX."_edocument_download");
DROP TABLE IF EXISTS `{prefix}_edocument`;
CREATE TABLE IF NOT EXISTS `{prefix}_edocument` (`id` int(11) unsigned NOT NULL auto_increment,`module_id` int(11) unsigned NOT NULL,`sender_id` int(11) unsigned NOT NULL,`reciever` text collate utf8_unicode_ci NOT NULL,`last_update` int(11) unsigned NOT NULL,`downloads` int(11) unsigned NOT NULL,`document_no` varchar(20) collate utf8_unicode_ci NOT NULL,`detail` text collate utf8_unicode_ci NOT NULL,`topic` varchar(50) collate utf8_unicode_ci NOT NULL,`ext` varchar(4) collate utf8_unicode_ci NOT NULL,`size` double unsigned NOT NULL,`file` varchar(15) collate utf8_unicode_ci NOT NULL,`ip` varchar(50) collate utf8_unicode_ci NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS `{prefix}_edocument_download`;
CREATE TABLE IF NOT EXISTS `{prefix}_edocument_download` (`id` int(10) unsigned NOT NULL auto_increment,`module_id` int(10) unsigned NOT NULL,`document_id` int(10) unsigned NOT NULL,`member_id` int(10) unsigned NOT NULL,`downloads` int(10) unsigned NOT NULL,`last_update` int(10) unsigned NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
DELETE FROM `{prefix}_emailtemplate` WHERE `module`='edocument';
INSERT INTO `{prefix}_emailtemplate` (`module`, `email_id`, `language`, `from_email`, `copy_to`, `name`, `subject`, `detail`, `last_update`, `last_send`) VALUES ('edocument','1','th','','','แจ้งการส่งเอกสาร','มีเอกสารส่งถึงคุณใน %WEBTITLE%','<div style="padding: 10px; background-color: rgb(247, 247, 247);">\r\n<table style="border-collapse: collapse;">\r\n	<tbody>\r\n		<tr>\r\n			<th style="border-width: 1px; border-style: none solid; border-color: rgb(59, 89, 152); padding: 5px; text-align: left; color: rgb(255, 255, 255); font-family: tahoma; font-size: 9pt; background-color: rgb(59, 89, 152);">มีเอกสารส่งถึงคุณใน %WEBTITLE%</th>\r\n		</tr>\r\n		<tr>\r\n			<td style="border-width: 1px; border-style: none solid solid; border-color: rgb(204, 204, 204) rgb(204, 204, 204) rgb(59, 89, 152); padding: 15px; line-height: 1.8em; font-family: tahoma; font-size: 9pt;">เรียนคุณ %FNAME% %LNAME%<br />\r\n			<br />\r\n			มีเอกสารใหม่ส่งถึงคุณ เมื่อ %TIME%<br />\r\n			<br />\r\n			คุณสามารถตรวจสอบรายการเอกสารของคุณได้ที่ <a href="%URL%">%URL%</a> (คุณอาจต้องเข้าระบบก่อน)</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="padding: 15px; color: rgb(153, 153, 153); font-family: tahoma; font-size: 8pt;">ด้วยความขอบคุณ <a href="mailto:%ADMINEMAIL%">เว็บมาสเตอร์</a></td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>\r\n','1393854792','0');
DELETE FROM `{prefix}_language` WHERE `owner`='edocument';
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('EDOCUMENT_FILE_EMPTY','text','edocument','1','กรุณาเลือกไฟล์เอกสาร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('EDOCUMENT_FORMAT_NO_EMPTY','text','edocument','1','กรุณาระบุรูปแบบของเลขที่เอกสารเช่น %04d');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('EDOCUMENT_NO_EMPTY','text','edocument','1','กรุณากรอกเลขที่ของเอกสาร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('EDOCUMENT_NO_EXISTS','text','edocument','1','มีเลขที่เอกสารนี้อยู่ก่อนแล้ว');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('EDOCUMENT_RECIEVER_EMPTY','text','edocument','1','กรุณาระบุกลุ่มผู้รับเอกสาร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('EDOCUMENT_SAVE_AND_SEND_SUCCESS','text','edocument','1','บันทึกและส่งอีเมล์เรียบร้อยแล้ว');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT','text','edocument','0','E-Document');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_DESCRIPTION_COMMENT','text','edocument','0','คำอธิบายหรือหมายเหตุเพิ่มเติมของเอกสาร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_DOWNLOAD_DETAILS','text','edocument','0','รายละเอียดการดาวน์โหลด');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_FILE_BROWSER_COMMENT','text','edocument','0','เลือกไฟล์ที่ต้องการนำส่ง ชนิด {TYPE} ขนาดไม่เกิน {SIZE}');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_FORMAT_NO_COMMENT','text','edocument','0','ระบุรูปแบบของเลขที่เอกสาร เช่น %04d หมายถึง เติมเลขศูนย์ให้ครบสี่หลัก เช่น 0001 เป็นต้น');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_ITEM','text','edocument','0','เอกสาร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_LIST','text','edocument','0','รายการเอกสาร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_MODERATOR_COMMENT','text','edocument','0','ขีดถูกเพื่อให้สมาชิกระดับนี้สามารถจัดการเอกสารดาวน์โหลดของผู้อื่นได้');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_NO','text','edocument','0','เลขที่เอกสาร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_NO_COMMENT','text','edocument','0','ระบุเลขที่ของเอกสาร ใช้สำหรับอ้างอิงเอกสาร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_RECIVE_GROUPS','text','edocument','0','เลือกกลุ่มผู้รับ');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_RECIVE_GROUPS_COMMENT','text','edocument','0','เลือกลุ่มผู้รับเอกสาร สามารถเลือกได้หลายกลุ่ม');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_SEND_EMAIL_COMMENT','text','edocument','0','เอกสารใหม่จะถูกส่งอีเมล์แจ้งไปยังผู้รับด้วย เมื่อเปิดใช้งานตัวเลือกนี้');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_SEND_EMAIL_TO','text','edocument','0','ส่งอีเมล์แจ้งไปยังสมาชิกด้วย');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_STATUS','text','edocument','0','แผนก');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_TOPIC','text','edocument','0','ชื่อเอกสาร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_TOPIC_COMMENT','text','edocument','0','ชื่อของเอกสาร จะถูกใช้เป็นชื่อไฟล์ด้วย ถ้าไม่ระบุจะเอามาจากชื่อของไฟล์ที่อัปโหลด (ไม่ต้องระบุนามสกุลของไฟล์)');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_UPLOAD_BY','text','edocument','0','อัปโหลดโดย');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EDOCUMENT_UPLOAD_DATE','text','edocument','0','อัปโหลดเมื่อ');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_FILE_SIZE','text','edocument','0','ขนาดไฟล์');