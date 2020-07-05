<?php

namespace Espo\Core\Utils\Traits;

trait DateTimeTrait {
    public function traverse_farsi($str) : string  {
        $farsi_chars = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $latin_chars = ['0', '1','2','3','4','5','6','7','8','9'];
        return str_replace($farsi_chars, $latin_chars, $str);
    }

    public function normlizationDate($data): object
    {

        $data = (array) $data;

        foreach ( $data as $key => $item){
            if($key == 'dateStart' || $key == 'dateEnd') {
                $data[$key] = $this->traverse_farsi($item);
            }
        }

        return (object) $data;
    }

    public function  checkDateIsJalali($dateStart): bool
    {
        if($dateStart == '') return false;

        $year = substr($dateStart,0 , 4);

        if($year > 1420) {
            return false;
        }

        return true;
    }

    public function jalaliToGregorian($dateStart): string
    {
        $date = substr($dateStart,0 , 10);
        $time = substr($dateStart,10 , 20);

        $date = PersianDate::toGregorian($date);

        $date = explode("-", $date);

        // if month is one char 2 must be change to 02
        if(strlen($date[1]) == 1) $date[1] = "0" . $date[1];
        // time in persian 64 min biggest default time

        return $date[0] . "-" . $date[1] . "-" . $date[2] .  " " . date('H:i:s', StrToTime($time) - (64*60));
    }

}
