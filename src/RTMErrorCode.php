<?php 

namespace highras\rtm;

class RTMErrorCode {
	public static $RTM_EC_INVALID_PID_OR_UID = 200001;
	public static $RTM_EC_INVALID_PID_OR_SIGN = 200002;
	public static $RTM_EC_INVALID_FILE_OR_SIGN_OR_TOKEN = 200003;
	public static $RTM_EC_ATTRS_WITHOUT_SIGN_OR_EXT = 200004;
	public static $RTM_EC_INVALID_MTYPE = 200005;
	public static $RTM_EC_SAME_SIGN = 200006;
	public static $RTM_EC_INVALID_FILE_MTYPE = 200007;

	public static $RTM_EC_FREQUENCY_LIMITED = 200010;
	public static $RTM_EC_REFRESH_SCREEN_LIMITED = 200011;
	public static $RTM_EC_KICKOUT_SELF = 200012;

	public static $RTM_EC_FORBIDDEN_METHOD = 200020;
	public static $RTM_EC_PERMISSION_DENIED = 200021;
	public static $RTM_EC_UNAUTHORIZED = 200022;
	public static $RTM_EC_DUPLCATED_AUTH = 200023;
	public static $RTM_EC_AUTH_DENIED = 200024;
	public static $RTM_EC_ADMIN_LOGIN = 200025;
	public static $RTM_EC_ADMIN_ONLY = 200026;

	public static $RTM_EC_LARGE_MESSAGE_OR_ATTRS = 200030;
	public static $RTM_EC_LARGE_FILE_OR_ATTRS = 200031;
	public static $RTM_EC_TOO_MANY_ITEMS_IN_PARAMETERS = 200032;
	public static $RTM_EC_EMPTY_PARAMETER = 200033;
	
	public static $RTM_EC_NOT_IN_ROOM = 200040;
	public static $RTM_EC_NOT_GROUP_MEMBER = 200041;
	public static $RTM_EC_MAX_GROUP_MEMBER_COUNT = 200042;
	public static $RTM_EC_NOT_FRIEND = 200043;
	public static $RTM_EC_BANNED_IN_GROUP = 200044;
	public static $RTM_EC_BANNED_IN_ROOM = 200045;
	public static $RTM_EC_EMPTY_GROUP = 200046;
	public static $RTM_EC_MAX_ROOM_COUNT = 200047;
	public static $RTM_EC_MAX_FRIEND_COUNT = 200048;

	public static $RTM_EC_UNSUPPORTED_LANGUAGE = 200050;
	public static $RTM_EC_EMPTY_TRANSLATION = 200051;
	public static $RTM_EC_SEND_TO_SELF = 200052;
	public static $RTM_EC_DUPLCATED_MID = 200053;
	public static $RTM_EC_SENSITIVE_WORDS = 200054;
	public static $RTM_EC_NOT_ONLINE = 200055;
	public static $RTM_EC_TRANSLATION_ERROR = 200056;
	public static $RTM_EC_PROFANITY_STOP = 200057;
	public static $RTM_EC_NO_CONFIG_IN_CONSOLE = 200060;
	public static $RTM_EC_UNSUPPORTED_TRASNCRIBE_TYPE = 200061;

	public static $RTM_EC_MESSAGE_NOT_FOUND = 200070;

	public static $RTM_EC_UNKNOWN_ERROR = 200999; 
}
