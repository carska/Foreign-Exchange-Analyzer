<?php

class currencyHandler {

    //Argument: a  date formated as YYYY/MM/DD.
    //Method uses all other methods in class currencyHandler to do the complete calculation.
    public function doWork($inputDate) {
        //covert date string so that it matches input format of fixer.io
        $date = $this->formatDate($inputDate);

        //get rates of input date.
        $ratesOfInputDate = $this->getRates($date);

        //rates thirty days before input date
        $ratesTDB = $this->getRates($this->getEarlierDate($date));

        //get variation between date input and 30 days before
        $rateVariation = $this->getRateVariaton($ratesTDB, $ratesOfInputDate);

        //build and print table of data
        $result = $this->buildTable($rateVariation);
        return $result;
    }
    
    public function formatDate($dateString) {
        $pieces = explode("/", $dateString);
        $formattedDate = $pieces[0] . "-" . $pieces[1] . "-" . $pieces[2];
        return $formattedDate;
    }

    //Argument: array where key represents Currency and value represents Rate
    //Method returns complete html-table, Bootstrap is jused to color rows.
    public function buildTable($data) {
        $table = '<table class="table">'
                . '<thead>'
                . '<tr>'
                . '<th>Currency</th>'
                . '<th>Rate (%)</th>'
                . '</tr>'
                . '</thead>'
                . '<tbody>';

        foreach ($data as $key => $val) {
            if (floatval($val) >= 1) {
                $table .= '<tr class="alert alert-success">';
                
            } elseif (floatval($val) < -1) {
                $table .= '<tr class="alert alert-danger">';
                
            } elseif (floatval($val) < 0) {
                $table .= '<tr class="alert alert-warning">';
                
            } else {
                $table .= '<tr>';
            }

            $table .= '<td>' . $key . '</td>'
                    . '<td>' . $val . '</td>'
                    . '</tr>';
        }

        $table .= '</tbody>'
                . '</table>';

        return $table;
    }

    //Method takes two arrays with rates as arguments. 
    //$firstRates: rates from earliest date. $secondRates: rates from input date
    //Method returns an array with rate variation of two arrays
    public function getRateVariaton($firstRates, $secondRates) {
        $rateVariation = array();
        foreach ($firstRates as $key => $val) {
            $rateVariation[$key] = round((($secondRates->$key / $val) - 1) * 100, 2);
        }
        arsort($rateVariation);

        return $rateVariation;
    }

    //get rates from specific date. Argument: "YYYY-MM-DD".
    public function getRates($date) {
        $url = 'https://api.fixer.io/' . $date;
        $json = file_get_contents($url);
        $info = json_decode($json);

        return $info->rates;
    }

    //takes a date (String) as argument, returns date (String) 30 days prior the input date. Argument: "YYYY-MM-DD".
    public function getEarlierDate($date) {

        return date_create($date)->modify('-30 day')->format('Y-m-d');
    }

}
?>

