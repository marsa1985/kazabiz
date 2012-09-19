<?php

/* ------------------------------------------------------------------------
  # En Masse - Social Buying Extension 2010
  # ------------------------------------------------------------------------
  # By Matamko.com
  # Copyright (C) 2010 Matamko.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.matamko.com
  # Technical Support:  Visit our forum at www.matamko.com
  ------------------------------------------------------------------------- */

class DatetimeWrapper {

    const CST_STRING_DEFAULT_TIMEZONE = 'UTC';    

    public function __construct() {
        $config =& JFactory::getConfig();
        $offset = $config->getValue('config.offset');          
        if (isset($offset)  && !is_null($offset)) {
            self::setTimezone($offset);
        } else {
            self::setTimezone(self::CST_STRING_DEFAULT_TIMEZONE);
        }
    }
    
    public static function getTimezone(){
        $JApp = JFactory::getApplication();
        $offset = $JApp->getCfg('offset');
        return $offset;
    }
    
    public static function setTimezone($sTimezone){
        date_default_timezone_set($sTimezone);
    }

    /**
     * To give the date & time of now
     *  
     * @return string 	The current Date Time in YYYY-MM-DD HH:MM:SS format
     */
    public static function getDatetimeOfNow($format = "Y-m-d H:i:s") {        
        $offset = self::getTimezone();
        self::setTimezone($offset);
        return date($format, time() + ($offset * 60 * 60));
    }

    /**
     * To give the date of now
     *  
     * @return string 	The current Date in YYYY-MM-DD format
     */
    public static function getDateOfNow($format = "Y-m-d") {        
        $offset = self::getTimezone();
        self::setTimezone($offset);
        return date($format, time() + ($offset * 60 * 60));
    }

    public static function getDatetimeFromStr($str = null, $format = "Y-m-d H:i:s") {
        if ($str == null || $str == "")
            return null;

        $offset = self::getTimezone();
        self::setTimezone($offset);
        return date($format, strtotime($str));
    }

    public static function getDateFromStr($str = null, $format = "Y-m-d") {
        if ($str == null || $str == "")
            return null;

        $offset = self::getTimezone();
        self::setTimezone($offset);
        return date($format, strtotime($str));
    }

    /**
     * To create an date & time in the standard YYYY-MM-DD HH:MM:SS
     *  
     * @params int year
     * @params int month
     * @params int day
     * @params int hour
     * @params int minute
     * @params int second
     * 
     * @return string 	The Date & Time in YYYY-MM-DD HH:MM:SS format
     */
    public static function mkDatetime($year, $month, $day, $hour, $minute, $second, $format = "Y-m-d H:i:s") {
        $offset = self::getTimezone();
        self::setTimezone($offset);
        return date($format, mktime($hour, $minute, $second, $month, $day, $year));
    }

    /**
     * To create a Future date & time in the standard YYYY-MM-DD HH:MM:SS
     *  
     * @params int year
     * @params int month
     * @params int day
     * @params int hour
     * @params int minute
     * @params int second
     * 
     * @return string 	The Date & Time in YYYY-MM-DD HH:MM:SS format
     */
    public static function mkFutureDatetime($nowDatetime, $year=0, $month=0, $day=0, $hour=0, $minute=0, $second=0) {
        $dtArray = DatetimeWrapper::extractFromDatetime($nowDatetime);
        return DatetimeWrapper::mkDatetime($dtArray['year'] + $year, $dtArray['month'] + $month, $dtArray['day'] + $day, $dtArray['hour'] + $hour, $dtArray['minute'] + $minute, $dtArray['second'] + $second);
    }

    /**
     * To create a Future date & time in the standard YYYY-MM-DD HH:MM:SS
     * @params int second
     * 
     * @return string 	The Date & Time in YYYY-MM-DD HH:MM:SS format
     */
    public static function mkFutureDatetimeSecFromNow($nowDatetime, $second=0) {
        $dtArray = DatetimeWrapper::extractFromDatetime($nowDatetime);
        return DatetimeWrapper::mkDatetime($dtArray['year'] + 0, $dtArray['month'] + 0, $dtArray['day'] + 0, $dtArray['hour'] + 0, $dtArray['minute'] + 0, $dtArray['second'] + $second);
    }

    /**
     * To create an date & time in the standard YYYY-MM-DD
     *  
     * @params int year
     * @params int month
     * @params int day
     * 
     * @return string 	The current Date in YYYY-MM-DD format
     */
    public static function mkDate($year, $month, $day, $format = "Y-m-d") {
        $offset = self::getTimezone();
        self::setTimezone($offset);
        return date($format, mktime(0, 0, 0, $month, $day, $year));
    }

    /**
     * To extract an Array of the date time info out of the YYYY-MM-DD HH:MM:SS formatted date time
     * The array returned will have the following elements
     * 'year', 'month', 'day', 'hour', 'minute', 'second'
     *  
     * @params string 	$dateTime 	Date & Time in YYYY-MM-DD HH:MM:SS format
     * 
     * @return string[] 
     */
    public static function extractFromDatetime($datetime) {
        $returnArray = array();
        if (isset($datetime)) {
            $tempArray = explode(" ", $datetime);
            if (isset($tempArray[0])) {
                $yearMonthDay = $tempArray[0];
            }
            if (isset($tempArray[1])) {
                $hourMinuteSecond = $tempArray[1];
            }
        }
        if (isset($yearMonthDay)) {
            $tempDateArray = explode("-", $yearMonthDay);
            $returnArray['year'] = $tempDateArray[0];
            $returnArray['month'] = $tempDateArray[1];
            $returnArray['day'] = $tempDateArray[2];
        }
        if (isset($hourMinuteSecond)) {
            $tempTimeArray = explode(":", $hourMinuteSecond);
            $returnArray['hour'] = $tempTimeArray[0];
            $returnArray['minute'] = str_pad($tempTimeArray[1], 2, "0", STR_PAD_LEFT);
            $returnArray['second'] = str_pad($tempTimeArray[2], 2, "0", STR_PAD_LEFT);
        }
        return $returnArray;
    }

    public static function numOfSecBetweenDatetime($dateTime1, $dateTime2) {
        $tempSec = strtotime($dateTime1) - strtotime($dateTime2);

        return $tempSec;
    }

    public static function getDisplayDatetime($datetime, $format = "j M Y, D (g:ia)") {
        $offset = self::getTimezone();
        if ($datetime == "" || $datetime == null)
            return null;
        if ($datetime == "9999-12-31 23:59:59")
            return null;
        if ($datetime == "0000-00-00 00:00:00")
            return null;

        
        $datetimeArray = self::extractFromDatetime($datetime);
        self::setTimezone($offset);
        $timestamp = mktime($datetimeArray['hour'], $datetimeArray['minute'], $datetimeArray['second'], $datetimeArray['month'], $datetimeArray['day'], $datetimeArray['year']);
        if ($timestamp == 0)
            return $datetime;
        else {
            $returnDisplay = date($format, $timestamp);
            return $returnDisplay;
        }
    }

    public static function getDisplayDate($datetime, $format = "j M Y, D") {
        $offset = self::getTimezone();
        if ($datetime == "" || $datetime == null)
            return null;

        $datetimeArray = self::extractFromDatetime($datetime);
        self::setTimezone($offset);
        $returnDisplay = date($format, mktime(0, 0, 0, $datetimeArray['month'], $datetimeArray['day'], $datetimeArray['year']));
        return $returnDisplay;
    }

    public static function calculateAge($birthday) {
        return floor((time() - strtotime($birthday)) / 31556926);
    }

}

?>