<?php die('File not found !')?>
ALTER TABLE `{prefix}_user` ADD `ip` VARCHAR(50) NOT NULL 
DROP TABLE IF EXISTS `{prefix}_emailtemplate`;
CREATE TABLE IF NOT EXISTS `{prefix}_emailtemplate` (`id` int(10) unsigned NOT NULL auto_increment,`module` varchar(20) collate utf8_unicode_ci NOT NULL,`email_id` int(10) unsigned NOT NULL,`language` varchar(2) collate utf8_unicode_ci NOT NULL,`from_name` text collate utf8_unicode_ci NOT NULL,`from_email` text collate utf8_unicode_ci NOT NULL,`copy_to` text collate utf8_unicode_ci NOT NULL,`name` text collate utf8_unicode_ci NOT NULL,`subject` text collate utf8_unicode_ci NOT NULL,`detail` text collate utf8_unicode_ci NOT NULL,`last_update` int(11) unsigned NOT NULL,`last_send` int(11) unsigned NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `{prefix}_emailtemplate` (`module`, `email_id`, `language`, `from_name`, `from_email`, `copy_to`, `name`, `subject`, `detail`, `last_update`, `last_send`) VALUES ('member','1','th','เว็บมาสเตอร์','{NOREPLY}','','ตอบรับการสมัครสมาชิกใหม่ (ยืนยันสมาชิก)','ตอบรับการสมัครสมาชิก %WEBTITLE%','<div style=\"padding: 40px; margin-right: 40px; display: block; background-color: rgb(247, 247, 247);\">\r\n	<table style=\"width: 100%; border-collapse: collapse;\">\r\n		<tbody>\r\n			<tr>\r\n				<th style=\"border-width: 1px; border-style: none solid; border-color: rgb(59, 89, 152); padding: 5px; text-align: left; color: rgb(255, 255, 255); font-family: tahoma; font-size: 9pt; background-color: rgb(59, 89, 152);\">\r\n					ยินดีต้อนรับสมาชิกใหม่ %WEBTITLE%</th>\r\n			</tr>\r\n			<tr>\r\n				<td style=\"border-width: 1px; border-style: none solid solid; border-color: rgb(204, 204, 204) rgb(204, 204, 204) rgb(59, 89, 152); padding: 15px; line-height: 1.8em; font-family: tahoma; font-size: 9pt;\">\r\n					ขอขอบคุณสำหรับการลงทะเบียนกับเรา บัญชีใหม่ของคุณได้รับการติดตั้งเรียบร้อยแล้วและคุณสามารถเข้าระบบได้โดยใช้รายละเอียดด้านล่างนี้<br />\r\n					<br />\r\n					ชื่อสมาชิก : <strong>%USERNAME%</strong><br />\r\n					รหัสผ่าน&nbsp; : <strong>%PASSWORD%</strong><br />\r\n					<br />\r\n					ก่อนอื่นคุณต้องกลับไปยืนยันการสมัครสมาชิกที่ <a href=\"%WEBURL%/modules/member/activate.php?id=%ID%\">%WEBURL%/modules/member/activate.php?id=%ID%</a></td>\r\n			</tr>\r\n			<tr>\r\n				<td style=\"padding: 15px; color: rgb(153, 153, 153); font-family: tahoma; font-size: 8pt;\">\r\n					ด้วยความขอบคุณ <a href=\"mailto:%ADMINEMAIL%\">เว็บมาสเตอร์</a></td>\r\n			</tr>\r\n		</tbody>\r\n	</table>\r\n</div>\r\n','1331206353','0');
INSERT INTO `{prefix}_emailtemplate` (`module`, `email_id`, `language`, `from_name`, `from_email`, `copy_to`, `name`, `subject`, `detail`, `last_update`, `last_send`) VALUES ('member','2','th','เว็บมาสเตอร์','{NOREPLY}','','ตอบรับการสมัครสมาชิกใหม่ (ไม่ต้องยืนยันสมาชิก)','ตอบรับการสมัครสมาชิก %WEBTITLE%','<div style=\"padding: 40px; margin-right: 40px; display: block; background-color: rgb(247, 247, 247);\">\r\n	<table style=\"width: 100%; border-collapse: collapse;\">\r\n		<tbody>\r\n			<tr>\r\n				<th style=\"border-width: 1px; border-style: none solid; border-color: rgb(59, 89, 152); padding: 5px; text-align: left; color: rgb(255, 255, 255); font-family: tahoma; font-size: 9pt; background-color: rgb(59, 89, 152);\">\r\n					ยินดีต้อนรับสมาชิกใหม่ %WEBTITLE%</th>\r\n			</tr>\r\n			<tr>\r\n				<td style=\"border-width: 1px; border-style: none solid solid; border-color: rgb(204, 204, 204) rgb(204, 204, 204) rgb(59, 89, 152); padding: 15px; line-height: 1.8em; font-family: tahoma; font-size: 9pt;\">\r\n					ขอขอบคุณสำหรับการลงทะเบียนกับเรา บัญชีใหม่ของคุณได้รับการติดตั้งเรียบร้อยแล้วและคุณสามารถเข้าระบบได้โดยใช้รายละเอียดด้านล่างนี้<br />\r\n					<br />\r\n					ชื่อสมาชิก : <strong>%USERNAME%</strong><br />\r\n					รหัสผ่าน&nbsp; : <strong>%PASSWORD%</strong><br />\r\n					<br />\r\n					คุณสามารถกลับไปเข้าระบบได้ที่ <a href=\"%WEBURL%\">%WEBURL%</a></td>\r\n			</tr>\r\n			<tr>\r\n				<td style=\"padding: 15px; color: rgb(153, 153, 153); font-family: tahoma; font-size: 8pt;\">\r\n					ด้วยความขอบคุณ <a href=\"mailto:%ADMINEMAIL%\">เว็บมาสเตอร์</a></td>\r\n			</tr>\r\n		</tbody>\r\n	</table>\r\n</div>\r\n','1331208364','0');
INSERT INTO `{prefix}_emailtemplate` (`module`, `email_id`, `language`, `from_name`, `from_email`, `copy_to`, `name`, `subject`, `detail`, `last_update`, `last_send`) VALUES ('member','3','th','เว็บมาสเตอร์','{NOREPLY}','','ขอรหัสผ่านใหม่','รหัสผ่านของคุณใน %WEBTITLE%','<div style=\"padding: 40px; margin-right: 40px; display: block; background-color: rgb(247, 247, 247);\">\r\n	<table style=\"width: 100%; border-collapse: collapse;\">\r\n		<tbody>\r\n			<tr>\r\n				<th style=\"border-width: 1px; border-style: none solid; border-color: rgb(59, 89, 152); padding: 5px; text-align: left; color: rgb(255, 255, 255); font-family: tahoma; font-size: 9pt; background-color: rgb(59, 89, 152);\">\r\n					รหัสผ่านของคุณใน %WEBTITLE%</th>\r\n			</tr>\r\n			<tr>\r\n				<td style=\"border-width: 1px; border-style: none solid solid; border-color: rgb(204, 204, 204) rgb(204, 204, 204) rgb(59, 89, 152); padding: 15px; line-height: 1.8em; font-family: tahoma; font-size: 9pt;\">\r\n					รหัสผ่านใหม่ของคุณถูกส่งมาจากระบบอัตโนมัติ เมื่อ %TIME%<br />\r\n					ไม่ว่าคุณจะได้ทำการขอรหัสผ่านใหม่หรือไม่ก็ตาม โปรดใช้รหัสผ่านใหม่นี้กับบัญชีของคุณ<br />\r\n					(ถ้าคุณไม่ได้ดำเนินการนี้ด้วยตัวเอง อาจมีผู้พยายามเข้าไปเปลี่ยนแปลงข้อมูลส่วนตัวของคุณ)<br />\r\n					<br />\r\n					ชื่อผู้ใช้ : <strong>%USERNAME%</strong><br />\r\n					รหัสผ่าน : <strong>%PASSWORD%</strong><br />\r\n					<br />\r\n					คุณสามารถกลับไปเข้าระบบและแก้ไขข้อมูลส่วนตัวของคุณใหม่ได้ที่ <a href=\"%WEBURL%\">%WEBURL%</a></td>\r\n			</tr>\r\n			<tr>\r\n				<td style=\"padding: 15px; color: rgb(153, 153, 153); font-family: tahoma; font-size: 8pt;\">\r\n					ด้วยความขอบคุณ <a href=\"mailto:%ADMINEMAIL%\">เว็บมาสเตอร์</a></td>\r\n			</tr>\r\n		</tbody>\r\n	</table>\r\n</div>\r\n','1331208279','0');
INSERT INTO `{prefix}_emailtemplate` (`module`, `email_id`, `language`, `from_name`, `from_email`, `copy_to`, `name`, `subject`, `detail`, `last_update`, `last_send`) VALUES ('share','1','th','เว็บมาสเตอร์','{NOREPLY}','','ส่งอีเมล์บอกเพื่อน','ส่งอีเมล์บอกเพื่อน','<div style=\"padding: 40px; margin-right: 40px; display: block; background-color: rgb(247, 247, 247);\">\r\n	<table style=\"width: 100%; border-collapse: collapse;\">\r\n		<tbody>\r\n			<tr>\r\n				<th style=\"border-width: 1px; border-style: none solid; border-color: rgb(59, 89, 152); padding: 5px; text-align: left; color: rgb(255, 255, 255); font-family: tahoma; font-size: 9pt; background-color: rgb(59, 89, 152);\">\r\n					ส่งอีเมล์บอกเพื่อน</th>\r\n			</tr>\r\n			<tr>\r\n				<td style=\"border-width: 1px; border-style: none solid solid; border-color: rgb(204, 204, 204) rgb(204, 204, 204) rgb(59, 89, 152); padding: 15px; line-height: 1.8em; font-family: tahoma; font-size: 9pt;\">\r\n					เพื่อนของคุณ <strong>%SENDER%</strong><br />\r\n					<br />\r\n					ได้แบ่งปันลิงค์นี้ให้กับคุณ<br />\r\n					<br />\r\n					<a href=\"%URL%\">%URL%</a><br />\r\n					<br />\r\n					ด้วยความขอบคุณ</td>\r\n			</tr>\r\n			<tr>\r\n				<td style=\"padding: 15px; color: rgb(153, 153, 153); font-family: tahoma; font-size: 8pt;\">\r\n					จดหมายนี้ถูกส่งมาโดยเพื่อนของคุณที่ลงทะเบียนไว้กับเว็บไซต์ <a href=\"%WEBURL%\">%WEBTITLE%</a> เมื่อ %TIME%</td>\r\n			</tr>\r\n		</tbody>\r\n	</table>\r\n</div>\r\n','1331209140','0');