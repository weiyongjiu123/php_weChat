# php_weChat

�йر��ļ����µĴ���˵��

remind/crontab ��linux����ʱ��� app/weChatPublic.sql �Ǻ�̨��Ҫ��mysql�����ݿ��

���⻹��Ҫ���ù��ںŲ˵���ť����ť��������

{ "button":[ { "name":"��������", "sub_button":[ { "type":"click", "name":"��������", "key":"setDayRemindOpen" }, { "type":"click", "name":"��ǰ����", "key":"setBeforeRemindOpen" } ] },{ "name":"�ر�����", "sub_button": [ { "type":"click", "name":"��������", "key":"setDayRemindClose" },{ "type":"click", "name":"��ǰ����", "key":"setBeforeRemindClose" } ] },{ "name":"�ҵ�", "sub_button":[ { "type":"click", "name":"����״̬", "key":"getRemindStatus" },{ "type":"click", "name":"�鿴�α�", "key":"getSchedule" } ] } ] }