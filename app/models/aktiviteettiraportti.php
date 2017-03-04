<?php

class Aktiviteettiraportti extends BaseModel {
    public $aktiviteettiraportti;

    public function aktiviteettiraportti($aanestys) { //Aktiviteettiraportti desiileinä
        $alkamisaika = new DateTime(date('d-m-Y', strtotime($aanestys->alkamisaika)));
        $loppumisaika = new DateTime(date('d-m-Y', strtotime($aanestys->loppumisaika)));
        $kymmenesosa = self::days_diff($alkamisaika, $loppumisaika) / 10;

        $aanet = $aanestys->find_aanet($aanestys->id);
        $aktiviteettiraportti = array_fill(0, 10, 0);
        $aikaikkuna_alku = $aanestys->alkamisaika;
        $aikaikkuna_loppu = $aanestys->alkamisaika;

        for ($i = 0; $i < 9; $i++) {
            $aikaikkuna_loppu = self::add_dayswithdate($aikaikkuna_loppu, $kymmenesosa);
            foreach ($aanet as $aani) {
                if ($aani->aika >= $aikaikkuna_alku && $aani->aika < $aikaikkuna_loppu) {
                    $aktiviteettiraportti[$i] ++;
                }
            }
            $aikaikkuna_alku = self::add_dayswithdate($aikaikkuna_alku, $kymmenesosa);
        }
        //Tehdään viimeisestä kymmenesosasta hieman suurempi, jos päivät eivät ole jaollisia kymmenellä
        foreach ($aanet as $aani) {
            if ($aani->aika >= $aikaikkuna_alku && $aani->aika <= $aanestys->loppumisaika) {
                $aktiviteettiraportti[9] ++;
            }
        }
        $this->aktiviteettiraportti = $aktiviteettiraportti;
        return $aktiviteettiraportti;
    }

    function koko() {
        return array_sum($this->aktiviteettiraportti);
    }

    function add_dayswithdate($date, $days) {
        $date = strtotime("+" . $days . " days", strtotime($date));
        return date("Y-m-d", $date);
    }

    public function days_diff($d1, $d2) {
        $x1 = self::days($d1);
        $x2 = self::days($d2);

        if ($x1 && $x2) {
            return abs($x1 - $x2);
        }
    }

    public function days($x) {
        if (get_class($x) != 'DateTime') {
            return false;
        }

        $y = $x->format('Y') - 1;
        $days = $y * 365;
        $z = (int) ($y / 4);
        $days += $z;
        $z = (int) ($y / 100);
        $days -= $z;
        $z = (int) ($y / 400);
        $days += $z;
        $days += $x->format('z');

        return $days;
    }

}
