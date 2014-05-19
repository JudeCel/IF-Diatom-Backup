<?php
// Calculate the age from a given birth date
// Example: GetAge("1986-06-18");
function GetAge($Birthdate)
{
    // Explode the date into meaningful variables
    list($BirthYear, $BirthMonth, $BirthDay) = explode("-", $Birthdate);

    $year_value = (int) $BirthYear;

    $age = 'No Date of Birth';    
    if($year_value){
        // Find the differences
        $YearDiff = date("Y") - $BirthYear;
        $MonthDiff = date("m") - $BirthMonth;
        $DayDiff = date("d") - $BirthDay;

        // If the birthday has not occured this year
        if (($DayDiff < 0 && $MonthDiff === 0) || $MonthDiff < 0){
          $YearDiff--;
        }

        $age = $YearDiff;
    }

    return $age;
}
?>