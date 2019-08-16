<?php


namespace CommonBase;


class FormatterBase
{
    public static function formatDate($datetime)
    {
        if (!$datetime instanceof \DateTime) {
            $datetime = new \DateTime(($datetime));
        }
        $today = new \DateTime('today');
        $daysDiff = $datetime->diff($today);
        if ($datetime > $today && $daysDiff->d == 0)
            $date = 'dziś';
        else if ($datetime > $today && $daysDiff->d == 1)
        $date = 'jutro';
        else if ($datetime <=> $today && $daysDiff->d == 0)
        $date = 'wczoraj';
        else
            $date = $datetime->format('d.m.Y');
        $time = $datetime->format('H:i:s');
        return "$date $time";
    }
}