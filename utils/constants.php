<?php

namespace Voxel;

if ( ! defined('ABSPATH') ) {
	exit;
}

const REG_MATCH_TAGS           = '/\@(?P<group>[a-zA-Z0-9_]+)\((?P<property>.*?(?<!\\\))\)(?P<modifiers>(?:\.[a-zA-Z0-9_]+(?:\(.*?(?<!\\\)\)))+)?/ims';
const REG_MATCH_MODIFIERS      = '/\.(?P<name>(?:[a-zA-Z0-9_])+)(?:\((?P<args>.*?(?<!\\\))\))/ims';
const REG_SPLIT_ARGS           = '/(?<!\\\),/ims';
const REG_UNESCAPE_ARG         = '/(?<!\\\)\\\([,)])/ims';
const REG_MATCH_DYNAMIC_STRING =  '/@tags\(\)(?P<dynamic_string>.*?)@endtags\(\)/ims';
const PREVIEW_DTAGS_IDENTIFIER = '<span hidden dtags></span>';

const T_ANY    = 'any';
const T_OBJECT = 'object';
const T_STRING = 'string';
const T_NUMBER = 'number';
const T_DATE   = 'date';
const T_EMAIL  = 'email';
const T_URL    = 'url';

const FOLLOW_REQUESTED = 0;
const FOLLOW_ACCEPTED  = 1;
const FOLLOW_BLOCKED   = -1;
const FOLLOW_NONE      = null;
