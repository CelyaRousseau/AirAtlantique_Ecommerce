<?php
namespace AirAtlantique\GeneratorBundle\Service;

use AirAtlantique\GeneratorBundle\Service\PdfGenerator;
use AirAtlantique\GeneratorBundle\Service\FlightException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UtilFlight
{

    private $path;

    function __construct($kernel) {
        $this->path = $kernel->locateResource('@GeneratorBundle/Resources/public/json');
    }

    public function flightConstruct()
    {
        $count = 1;
        $max = 500;
        $parsed_duration = $this->unstore('durations.json');
        $parsed_airports = $this->unstore('airports.json');
        $parsed_model = $this->unstore('models.json');
        $size = sizeof($parsed_duration->{'durations'});
        
        $sizeC;
        $format = "Y-m-d H:i";
        $date = date("Y-m-d");
        for ($i=1; $i <= 60; $i++) {
            for ($j=1; $j <= $max; $j++) {
                $flight["IdFlight"]=(double)$count;
        ### Random Airport ###
                $startArriveAirport = $parsed_duration->{'durations'}[rand(0,$size-1)];
                $startAirport=null;
                $arriveAirport=null;
                foreach ($parsed_airports->{"airports"} as $value) {
                    if($value->{"city"}==$startArriveAirport->{"start"})
                    {
                        $startAirport=$value;
                    }
                    if($value->{"city"}==$startArriveAirport->{"arrive"})
                    {
                        $arriveAirport=$value;
                    }
                    if(!empty($startAirport) && !empty($arriveAirport))
                    {
                        break;
                    }
                }
        ### Time start/arrive ###
                $start = \DateTime::createFromFormat($date." H:i", date($date." H:i",rand(0,time())));
                $start->add(new \DateInterval('P'.$i.'D'));
                $flight["startTime"]=$start->format($format);
                $timeexplode = explode(":", $startArriveAirport->{"duration"});
                $end=$start;
                $end->add(new \DateInterval('PT'.$timeexplode[0].'H'.$timeexplode[1].'M'));
                $flight["endTime"]=$end->format($format);
                if($j<($max/2))
                {
                    $flight["startAirport"]=$startAirport;
                    $flight["arriveAirport"]=$arriveAirport;
        ### Flight Name ###
                    $flight["flightName"]=$startArriveAirport->{"start"}."<->".$startArriveAirport->{"arrive"};
                }else
                {
                    $flight["startAirport"]=$arriveAirport;
                    $flight["arriveAirport"]=$startAirport;
        ### Flight Name ###
                    $flight["flightName"]=$startArriveAirport->{"arrive"}."<->".$startArriveAirport->{"start"};
                }
                $flight["duration"]=$startArriveAirport->{"duration"};
        ### Reference Flight ###
                $flight["reference"]=base64_encode(microtime());
                $rand = rand(0,1);
                if($timeexplode[0]>5)
                {
                    $sizeC = sizeof($parsed_model->{'long_courrier'})-1;
                    $model = $parsed_model->{'long_courrier'}[rand(0,$sizeC-1)];
                }else if($rand==1)
                {
                    $sizeC = sizeof($parsed_model->{'moyen_courrier'})-1;
                    $model = $parsed_model->{'moyen_courrier'}[rand(0,$sizeC-1)];
                }else
                {
                    $sizeC = sizeof($parsed_model->{'court_courrier'})-1;
                    $model = $parsed_model->{'court_courrier'}[rand(0,$sizeC-1)];
                }
                $flight["planeModel"]=$model;
                $flights["flights"][]=$flight;
                ++$count;
            }
        }
        $this->store("flight.json",$flights);
    }

    public function ruleEngine()
    {
        $validFlight=null;
        $parsed_flight = $this->unstore("flight.json");
        $parsed_airport = $this->unstore("airports.json");
        $parsed_model = $this->unstore("models.json");

        foreach ($parsed_airport->{"airports"} as $value) {
            $city=$value->{"city"};
            $airports[$city]["runwayNumber"]=intval($value->{"runwayNumber"});
        }

        foreach ($parsed_flight->{"flights"} as $value) {
            $success = true;

            $startAirport = $value->{"startAirport"};
            $arriveAirport = $value->{"arriveAirport"};
            if(!$this->validateDate($value->{"startTime"}) || !$this->validateDate($value->{"endTime"}))
            {
                new FlightException("Bad date format : ".$value->{"startTime"}." ".$value->{"endTime"});
        //throw new Exception("Bad date format : ".$value->{"startTime"}." ".$value->{"endTime"}, 1);
                $success=false;
            }

            if(!$this->checkFlight($startAirport,$value->{"startTime"},$airports))
            {
                $success=false;
            }

            if(!$this->checkFlight($arriveAirport,$value->{"endTime"},$airports))
            {
                $success=false;
            }

            if($value->{"flightName"} != ($startAirport->{"city"}."<->".$arriveAirport->{"city"}))
            {
                $success=false;
                new FlightException("Incompatibility between flight name and name of start/arrive City : ".$value->{"flightName"});
        //throw new Exception("Incompatibility between flight name and name of start/arrive City : ".$value->{"flightName"}, 1);
            }

            if(strlen($value->{"reference"}) != 28)
            {
                $success=false;
                new FlightException("Bad lenght for reference : ".$value->{"reference"});
        //throw new Exception("Bad lenght for reference", 1);

            }

            if(preg_match("/[^\w]/", $value->{"reference"}) || !preg_match("/[0-9]/", $value->{"reference"}) || !preg_match("/[a-zA-Z]/", $value->{"reference"}))
            {
                $success=false;
                new FlightException("Bad construction for reference : ".$value->{"reference"});
        //throw new Exception("Bad construction for reference", 1);
            }

            if(isset($reference[$value->{"reference"}]))
            {
                $success=false;
                new FlightException("Reference must be unique : ".$value->{"reference"});
        //throw new Exception("Reference must be unique", 1);

            }

            $planeModel=$value->{"planeModel"}->{"name"};
            $duration = explode(":",$value->{"duration"})[0];

            if($duration>5)
            {
                $find = $this->searchPlaneModelWithDuration("long_courrier",$planeModel,$parsed_model);
            }else
            {
                $find = $this->searchPlaneModelWithDuration("moyen_courrier",$planeModel,$parsed_model) || $this->searchPlaneModelWithDuration("court_courrier",$planeModel,$parsed_model);   
            }

            if(!$find)
            {
                $success=false;
                new FlightException("Model doesn't exist : ".$planeModel.". Duration : ".$duration.". Flight : ".$value->{"flightName"});
        //throw new Exception("Model doesn't exist : ".$planeModel, 1);
            }

            $reference[$value->{"reference"}]=1;

            $airports[$startAirport->{"city"}][$value->{"startTime"}]=1;
            $airports[$arriveAirport->{"city"}][$value->{"endTime"}]=1;


            if($success)
            {
                $validFlight["flights"][]=$value;
            }
        }

        $this->store("rule.json",$validFlight);
        //new PdfGenerator("name");
    }

    private function validateDate($date, $format = 'Y-m-d H:i')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    private function checkFlight($airport,$time,$airports)
    {
        $count=0;
        $city=$airport->{"city"};
        $dateTime = \DateTime::createFromFormat("Y-m-d H:i",$time);
        $dateTime->sub(new \DateInterval('PT30M'));

        for($i=0;$i<=60;++$i)
        {

            if(isset($airports[$city][$dateTime->format("Y-m-d H:i")]))
            {
                ++$count;
            }

            $dateTime->add(new \DateInterval('PT1M'));
        }

        if($count >= $airports[$city]["runwayNumber"])
        {
            new FlightException("flightNumber > runwayNumber : ".$time." ".$city);
            return false;
        //throw new Exception("flightNumber > runwayNumber", 1);
        }else
        {
            return true;
        }

    }

    private function searchPlaneModelWithDuration($type,$nameModel,$parsed_model)
    {
        $find = false;
        foreach ($parsed_model->{$type} as $value) {
            if($value->{"name"}==$nameModel)
            {
                $find=true;
                break;
            }
        }

        return $find;

    }

    private function searchPlaneModel($nameModel,$parsed_model)
    {
        foreach ($parsed_model as $type) {
            foreach ($type as $value) {
                if($value->{"name"}==$nameModel)
                {
                    return $value;
                }
            }
        }

    }

    private function store($file,$datas)
    {
        file_put_contents($this->path."/".$file,json_encode($datas));
    }

    private function unstore($file)
    {
        $content = json_decode(file_get_contents($this->path."/".$file));

        return $content;
    }
}