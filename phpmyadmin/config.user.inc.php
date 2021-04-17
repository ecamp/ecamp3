<?php

$sessionDuration = 60 * 60 * 24 * 7; // 60*60*24*7 = one week

$cfg['LoginCookieValidity'] = $sessionDuration;
ini_set('session.gc_maxlifetime', "{$sessionDuration}");
