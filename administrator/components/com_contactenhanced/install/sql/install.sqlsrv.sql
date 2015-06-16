IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_cf]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__ce_cf] (
	[id]  [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](max) NOT NULL,
	[label] [nvarchar](max) NOT NULL,
	[alias] [nvarchar](max) NOT NULL,
	[required] [smallint] NOT NULL DEFAULT '0',
	[type] [nvarchar](20) NOT NULL,
	[value] [nvarchar](max) NOT NULL,
	[ordering] [int] NOT NULL,
	[catid] [int] NOT NULL,
	[published] [smallint] NOT NULL DEFAULT '1',
	[attributes] [nvarchar](max),
	[tooltip] [nvarchar](250) DEFAULT NULL,
	[iscore] [smallint] NOT NULL DEFAULT '0',
	[params] [nvarchar](max),
	[access] [bigint] NOT NULL,
	[language] [nvarchar](7) NOT NULL,
	[metakey] [nvarchar](max)  NOT NULL,
	[metadesc] [nvarchar](max)  NOT NULL,
	CONSTRAINT [PK_#__ce_cf_id] PRIMARY KEY CLUSTERED 
	(
		[id] ASC
	)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)

  
)
  CREATE NONCLUSTERED INDEX [idx_language] ON [#__ce_cf]
    (
	    [language] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)

    CREATE NONCLUSTERED INDEX [idx_published] ON [#__ce_cf]
    (
	    [published] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
	
SET IDENTITY_INSERT #__ce_cf  ON

INSERT INTO #__ce_cf
(id, name, label,alias, required, type, value, ordering, catid, published, attributes, tooltip, iscore, params, access, language, metadesc, metakey)
SELECT  1, 'Name', '', 'name', 1, 'name', '', 1, 0, 1, '', '', 0, '{"field-width":"100","hide_field":"0","hide_field_label":"0","tooltip_behavior":"mouseover","advanced":"0","advanced-integration-name":""}', 1, '*', '',''
UNION ALL
SELECT  2, 'Email', '', 'email', 1, 'email', '', 2, 0, 1, '', '', 0, '{"field-width":"100","hide_field":"0","hide_field_label":"0","tooltip_behavior":"mouseover","advanced":"0","advanced-integration-name":""}', 1, '*', '',''
UNION ALL
SELECT  3, 'Subject', '','subject', 1, 'subject', '', 3, 0, 1, '', '', 0, '{"field-width":"100","hide_field":"0","hide_field_label":"0","tooltip_behavior":"mouseover","advanced":"0","advanced-integration-name":""}', 1, '*', '',''
UNION ALL
SELECT  4, 'Message', '', 'message', 1, 'multitext', '', 4, 0, 1, '', '', 0, '{"field-width":"100","hide_field":"0","hide_field_label":"0","tooltip_behavior":"mouseover","advanced":"0","advanced-integration-name":""}', 1, '*', '',''

SET IDENTITY_INSERT #__ce_cf  OFF




END;


IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_cv]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__ce_cv] (
	[id]  [bigint] IDENTITY(1,1) NOT NULL,
	[text] [nvarchar](250) NOT NULL,
	[name] [nvarchar](250) NOT NULL,
	[value] [nvarchar](250) NOT NULL,
	[description] [nvarchar](max) NOT NULL,
	[type] [nvarchar](100) DEFAULT NULL,
	[ordering] [int] NOT NULL,
	[catid] [bigint] NOT NULL,
	[parent] [nvarchar](100) DEFAULT NULL,
	[published] [smallint] NOT NULL DEFAULT '1',
	[access] [bigint] NOT NULL,
	[params] [nvarchar](max),
	[language] [nvarchar](7) NOT NULL, 
	CONSTRAINT [PK_#__ce_cv_id] PRIMARY KEY CLUSTERED 
	(
		[id] ASC
	)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
    

)

    CREATE NONCLUSTERED INDEX [idx_ce_cv_text] ON [#__ce_cv]
    (
	    [text] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)

    CREATE NONCLUSTERED INDEX [idx_language] ON [#__ce_cv]
    (
	    [language] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;


IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_details]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__ce_details] (
	[id]  [int] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](250) NOT NULL DEFAULT '',
	[alias] [nvarchar](250) NOT NULL DEFAULT '',
	[con_position] [nvarchar](250) DEFAULT NULL,
	[address] text,
	[suburb] [nvarchar](100) DEFAULT NULL,
	[state] [nvarchar](100) DEFAULT NULL,
	[country] [nvarchar](100) DEFAULT NULL,
	[postcode] [nvarchar](100) DEFAULT NULL,
	[telephone] [nvarchar](250) DEFAULT NULL,
	[fax] [nvarchar](250) DEFAULT NULL,
	[misc] [nvarchar](max),
	[sidebar] [nvarchar](max),
	[image] [nvarchar](250) DEFAULT NULL,
	[imagepos] [nvarchar](20) DEFAULT NULL,
	[email_to] [nvarchar](250) DEFAULT NULL,
	[default_con] [bigint] NOT NULL DEFAULT '0',
	[published] [smallint] NOT NULL DEFAULT '0',
	[checked_out] [bigint]  NOT NULL DEFAULT '0',
	[checked_out_time] [datetime] NOT NULL DEFAULT '1900-01-01 00:00:00',
	[ordering] [int] NOT NULL DEFAULT '0',
	[params] [nvarchar](max)  NOT NULL,
	[user_id] [int] NOT NULL DEFAULT '0',
	[catid] [int] NOT NULL DEFAULT '0',
	[access] [bigint] NOT NULL DEFAULT '0',
	[mobile] [nvarchar](250) NOT NULL DEFAULT '',
	[skype] [nvarchar](250) NOT NULL,
	[twitter] [nvarchar](250) NOT NULL,
	[facebook] [nvarchar](250) NOT NULL,
	[linkedin] [nvarchar](250) NOT NULL,
	[webpage] [nvarchar](250) NOT NULL DEFAULT '',
	[birthdate] [date] NULL,
	[sortname1] [nvarchar](250) NOT NULL,
	[sortname2] [nvarchar](250) NOT NULL,
	[sortname3] [nvarchar](250) NOT NULL,
	[language] [nvarchar](7) NOT NULL,
	[created] [datetime] NOT NULL DEFAULT '1900-01-01 00:00:00',
	[created_by] [bigint]  NOT NULL DEFAULT '0',
	[created_by_alias] [nvarchar](250) NOT NULL DEFAULT '',
	[modified] [datetime] NOT NULL DEFAULT '1900-01-01 00:00:00',
	[modified_by] [bigint]  NOT NULL DEFAULT '0',
	[metakey] [nvarchar](max)  NOT NULL,
	[metadesc] [nvarchar](max)  NOT NULL,
	[metadata] [nvarchar](max)  NOT NULL,
	[featured] [bigint] NOT NULL DEFAULT '0',
	[xreference] [nvarchar](50) NOT NULL,
	[publish_up] [datetime] NOT NULL DEFAULT '1900-01-01 00:00:00',
	[publish_down] [datetime] NOT NULL DEFAULT '1900-01-01 00:00:00',
	[lat] [nvarchar](20) NOT NULL,
	[lng] [nvarchar](20) NOT NULL,
	[zoom] [bigint] NOT NULL DEFAULT '15',
	[extra_field_1] [nvarchar](max) NOT NULL,
	[extra_field_2] [nvarchar](max) NOT NULL,
	[extra_field_3] [nvarchar](max) NOT NULL,
	[extra_field_4] [nvarchar](max) NOT NULL,
	[extra_field_5] [nvarchar](max) NOT NULL,
	[extra_field_6] [nvarchar](max) NOT NULL,
	[extra_field_7] [nvarchar](max) NOT NULL,
	[extra_field_8] [nvarchar](max) NOT NULL,
	[extra_field_9] [nvarchar](max) NOT NULL,
	[extra_field_10] [nvarchar](max) NOT NULL,
	CONSTRAINT [PK_#__ce_details_id] PRIMARY KEY CLUSTERED 
	(
		[id] ASC
	)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)

    

)

    CREATE NONCLUSTERED INDEX [idx_language] ON [#__ce_details]
    (
	    [language] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)

    CREATE NONCLUSTERED INDEX [idx_published] ON [#__ce_details]
    (
	    [published] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)

    CREATE NONCLUSTERED INDEX [idx_access] ON [#__ce_details]
    (
	    [access] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)

    CREATE NONCLUSTERED INDEX [idx_checked_out] ON [#__ce_details]
    (
	    [checked_out] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)


    CREATE NONCLUSTERED INDEX [idx_catid] ON [#__ce_details]
    (
	    [catid] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)


    CREATE NONCLUSTERED INDEX [idx_created_by] ON [#__ce_details]
    (
	    [created_by] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)


    CREATE NONCLUSTERED INDEX [idx_xreference] ON [#__ce_details]
    (
	    [xreference] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)

    CREATE NONCLUSTERED INDEX [idx_featured_catid] ON [#__ce_details]
    (
	    [featured] ASC,
	    [catid] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)


END;


IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_message_fields]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__ce_message_fields] (
	[id]  [bigint] IDENTITY(1,1) NOT NULL,
	[message_id] [bigint]  NOT NULL,
	[field_id] [bigint]  NOT NULL,
	[field_type] [nvarchar](20) NOT NULL,
	[value] [nvarchar](max) NOT NULL,
	[modified] [smallint] NOT NULL,
	CONSTRAINT [PK_#__ce_message_fields_id] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;



IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_messages]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__ce_messages] (
	[id]  [bigint] IDENTITY(1,1) NOT NULL,
	[parent] [bigint] NOT NULL DEFAULT '0',
	[from_name] [nvarchar](200) NOT NULL,
	[from_email] [nvarchar](100) NOT NULL,
	[from_id] [bigint]  NOT NULL DEFAULT '0',
	[email_to] [nvarchar](max) NOT NULL,
	[email_cc] [nvarchar](max) NOT NULL,
	[email_bcc] [nvarchar](max) NOT NULL,
	[subject] [nvarchar](250) NOT NULL,
	[contact_id] [int] NOT NULL,
	[catid] [bigint]  NOT NULL,
	[date] [datetime] NOT NULL,
	[reply_date] [datetime] NOT NULL,
	[replied_by] [bigint]  NOT NULL,
	[message] [nvarchar](max) NOT NULL,
	[message_html] [nvarchar](max) NOT NULL,
	[user_ip] [nvarchar](24) NOT NULL,
	[published] [smallint] NOT NULL DEFAULT '0',
	[access] [bigint] NOT NULL,
	[language] [nvarchar](7) NOT NULL,
	CONSTRAINT [PK_#__ce_messages_id] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
    
    
)
CREATE NONCLUSTERED INDEX [idx_catid] ON [#__ce_messages]
    (
	    [catid] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)


    CREATE NONCLUSTERED INDEX [idx_language] ON [#__ce_messages]
    (
	    [language] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)

END;




IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_template]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__ce_template] (
	[id]  [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](50) NOT NULL,
	[html] [nvarchar](max) NOT NULL,
	[type] [nvarchar](50) NOT NULL,
	[published] [smallint] NOT NULL,
	[access] [bigint]  NOT NULL,
	[language] [nvarchar](7) NOT NULL,
	[params] [nvarchar](max) NOT NULL,
	[created] [datetime] NOT NULL,
	[created_by] [bigint]  NOT NULL DEFAULT '0',
	[modified] [datetime] NOT NULL DEFAULT '1900-01-01 00:00:00',
	[modified_by] [bigint]  NOT NULL DEFAULT '0',
	[checked_out] [bigint]  NOT NULL DEFAULT '0',
	[checked_out_time] [datetime] NOT NULL DEFAULT '1900-01-01 00:00:00',
	CONSTRAINT [PK_#__ce_template_id] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)

    CREATE NONCLUSTERED INDEX [idx_template_name] ON [#__ce_template]
    (
	    [name] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)


    CREATE NONCLUSTERED INDEX [idx_language] ON [#__ce_template]
    (
	    [language] ASC
    )WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)

	
SET IDENTITY_INSERT #__ce_template  ON

INSERT INTO #__ce_template
(id, name, html, type, published, access, language, params, created, created_by, modified, modified_by, checked_out, checked_out_time)
SELECT  1, 'Default Email', '<table style="padding: 10px; background-color: #eeeeee; font-family: Arial; font-size: 11px;" border="0" cellspacing="0" cellpadding="0" width="100%"><tbody><tr><td align="center"><table style="background-color: #ffffff;" border="0" cellspacing="0" cellpadding="0" width="600"><tbody><tr><td style="color: #888888; padding: 10px;" align="left"><h1 style="color: #888888;">{site_name}</h1><hr style="color: #ab0e1f; background-color: #ab0e1f; height: 5px;" /></td></tr><tr><td style="padding: 10px;" valign="top"><p>{enquiry}<br /><br />{custom_fields}</p><p> </p><p>{attachments}</p><p>{system_info}</p></td></tr><tr><td style="background-color: #505050; color: #c8c8c8; padding: 20px;" height="61" align="left" valign="middle"><p>{contact_name} <br />{contact_address}, {contact_suburb} - {contact_state} - {contact_country} {contact_postcode}<br />{contact_telephone} | {contact_mobile}</p><p>Copyright \A9 {site_link}.</p><p>Sent date: {timestamp}</p></td></tr></tbody></table></td></tr></tbody></table><p> </p>', 'email', 1, 1, '*', '{"style-cf-label":"font-weight:bold;display:block;padding-top:8px","style-cf-value":""}', '1900-01-01 00:00:00', 0, '1900-01-01 00:00:00', 0, 0, '1900-01-01 00:00:00'
UNION ALL
SELECT  2, 'Ideal Support Copy Message', '<div style="margin: 0pt; padding: 0pt; background: #f0f0f0 none repeat scroll 0pt 0pt; width: 100%; color: #666666;"><div style="margin: 0pt auto; width: 600px;"><div style="background: #c01122 none repeat scroll 0% 0%; overflow: hidden; height: 80px; width: 600px;"><img src="http://idealextensions.com//components/com_contact_enhanced/templates/email/ideal_support/images/ideal_software_development_header.jpg" border="0" alt="http://idealextensions.com//components/com_contact_enhanced/templates/email/ideal_support/images/ideal_software_development_header.jpg" /></div><div id="ja-contentwrapper"><div style="margin: 0pt auto; padding: 10px; background: #ffffff none repeat scroll 0% 0%; width: 515px;"><div id="ja-content"><div style="margin: 0 10px;"><h1 style="border-bottom: 1px solid #cccccc; margin: 0pt 0pt 15px; padding: 10px; font-size: 150%;">{sitename}</h1><div style="border: 1px dashed #cccccc; margin: 3px; padding: 3px; background-color: #ffffdd; width: 180px; float: right;"><h3>Have you seen these resources?</h3><p>? <a href="http://idealextensions.com/component/option,com_moofaq/Itemid,24/id,2/view,categories/">FAQ</a>;</p><p>? <a href="http://idealextensions.com/component/option,com_kunena/Itemid,4/">Forum</a>;</p><p>? <a href="http://idealextensions.com/joomla/contact-enhanced/contact-enhanced-documentation.html">Contact Enhanced Documentation</a>;</p></div>{txt:Thanks for contacting us. We''ll reply to your email as soon as possible}.</div><div style="margin: 0 10px;"><br /> {txt:Below is a copy of your message}:<p>{custom_fields}</p><p>{attachments}</p><div style="border-top: 1px dashed #CCC;"><h3>In the meanwhile take a look at our other extensions:</h3><p><a href="http://idealextensions.com/joomla/extensions/component-contact-enhanced.html">Contact Enhanced</a> - <small>Contact  Form with a few advantages, such as: Google Maps integration, Custom  Fields, Captcha (ReCAPTCHA, SecurImage or MathGuard), User info ( IP  Address, Browser, Operating System and Screen resolution) and many more  enhancement.</small></p><p><a href="http://idealextensions.com/joomla/extensions/component-moofaq-frequently-asked-questions.html">MooFAQ</a> - <small>Display FAQ or any content category in an Accordion Style layout.</small></p><p><a href="http://idealextensions.com/joomla/extensions/module-opensource-ajax-recommend.html">Ajax Recommend</a> - <small>Allow your users to recommend your website to their friends.</small></p><p><a href="http://idealextensions.com/joomla/extensions/module-opensource-ajax-recommend.html">Ajax Contact</a> - <small>Allows visitors to contact your site administrator by e-mail very fast and secure, without the need to reload the page;</small></p><p><a href="http://idealextensions.com/joomla/extensions/component-name-meaning-dictionary.html">Name Meaning Dictionary</a> - <small>Has  over 4100 girls and boys names and meanings in the database, from 47  different origins, ideal for your baby or pregnancy related website.</small></p></div></div></div></div></div><div style="border-top: 3px solid #c01122; padding: 15px 10px; background: #34383b none repeat scroll 0pt 0pt; color: #cccccc; position: relative;"><div style="margin: 0pt; padding: 0pt; font-size: 11px; float: right; display: block;"><a href="http://idealextensions.com/index.php" style="padding: 0pt 10px; display: inline; color: #ffffff;"><span>Start Page</span></a> | <a href="http://idealextensions.com/terms-conditions.html" style="padding: 0pt 10px; display: inline; color: #ffffff;"><span>Terms &amp; Conditions</span></a> | <a href="http://idealextensions.com/privacy-policy.html" style="padding: 0pt 10px; display: inline; color: #ffffff;"><span>Privacy Policy</span></a></div><br style="clear: both;" /><div style="border-top: 1px dashed #CCC;"><br /> <small style="font-size: 70%; color: #999;"> {txt:Sent date}: {timestamp} </small></div></div></div></div>', 'email', 1, 1, '*', '{"style-cf-label":"","style-cf-value":""}', '1900-01-01 00:00:00', 0, '1900-01-01 00:00:00', 0, 0, '1900-01-01 00:00:00'
UNION ALL
SELECT 3, 'Simple Thanks with Custom Fields', '<h1 style="color: #888888;">Thank you for your email</h1><p>Below are the submitted fields</p><p>{enquiry}<br /><br />{custom_fields}</p><p>Sent date: {timestamp}</p>', 'resultpage', 1, 1, '*', '{"style-cf-label":"font-weight:bold;display:block;padding-top:8px","style-cf-value":""}', '1900-01-01 00:00:00', 0, '1900-01-01 00:00:00', 0, 0, '1900-01-01 00:00:00'

SET IDENTITY_INSERT #__ce_template  OFF
END;
