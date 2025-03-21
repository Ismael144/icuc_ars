<?php 

namespace App\traits;

/** Define Your User data table utilities here... */
trait Utils {
    /**
     * Formats date and time
     *
     * @param string|null $datetime
     * @return string
     */
    public function _format_date_and_time(string|null $datetime)
    {
        if ($datetime == null or $datetime == "0000-00-00 00:00:00") return "Not Signed In";
        
        $date = $this->format_date($datetime);
        $time = $this->time_format($datetime, "H:i");
        return $date . " at " . $this->_time_format_to_am_pm($time);
    }
    
    
    
    function _time_format_to_am_pm(?string $datetime, string $if_blank="Not Signed In"): string
    {
        if ($datetime == null or $datetime == "0000-00-00 00:00:00") return $if_blank;
        $formatted_date = $this->time_format($datetime, "H:i");
        $dateExploded = explode(":", $formatted_date);
        $minutes = $dateExploded[1];
        $dateDiff = (int)$dateExploded[0];
        
        $meridian = "";
        $hours = 0; 

        if ($dateDiff >= 12) {
            $hours = $dateDiff == 12 ? 12 : $dateDiff - 12; 
            $meridian = " pm";
        } else {
            $hours = $dateDiff == 0 ? 12 : $dateDiff;
            $meridian = " am";
        }

        $dateExploded[0] = $hours;
        $dateExploded[1] = ":";
        $dateExploded[2] = $minutes;
        $dateExploded[3] = $meridian; 

        return implode($dateExploded);
    }

    /**
     * Capitalizes the first_letter of the word
     *
     * @param string $word
     * @return string
     */
    public function _capitalize(string $word): string 
    {
        return ucwords($word);
    }
}