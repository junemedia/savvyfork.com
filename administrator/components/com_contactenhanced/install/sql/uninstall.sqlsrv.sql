IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_cf]') AND type in (N'U'))
BEGIN
	DROP TABLE #__ce_cf;
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_cv]') AND type in (N'U'))
BEGIN
	DROP TABLE #__ce_cv;
END;


IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_details]') AND type in (N'U'))
BEGIN
	DROP TABLE #__ce_details;
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_message_fields]') AND type in (N'U'))
BEGIN
	DROP TABLE #__ce_message_fields;
END;



IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_messages]') AND type in (N'U'))
BEGIN
	DROP TABLE #__ce_messages;
END;


IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ce_template]') AND type in (N'U'))
BEGIN
	DROP TABLE #__ce_template;
END;
