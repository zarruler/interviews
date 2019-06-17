<?php
namespace App\Controllers;

use App\Classes\Intervals\IntervalDispatcher;
use App\Classes\Intervals\Strategy\BetweenStartEnd;
use App\Classes\Intervals\Strategy\InnerEndIdentical;
use App\Classes\Intervals\Strategy\InnerStartEndIdentical;
use App\Classes\Intervals\Strategy\InnerStartIdentical;
use App\Classes\Intervals\Strategy\NoIntersections;
use App\Classes\Intervals\Strategy\OuterEndNearStart;
use App\Classes\Intervals\Strategy\OuterEndStartIntersect;
use App\Classes\Intervals\Strategy\OuterStartEndIntersect;
use App\Classes\Intervals\Strategy\OuterStartNearEnd;
use App\Classes\Intervals\Strategy\WideStartEnd;
use App\Models\Interval;
use Core\Config\ValidatorFactory;
use Core\Controller;
use Core\Database\IntervalValue;
use Core\Database\ModelRecord;
use Core\Header;
use Symfony\Component\HttpFoundation\Response;

class IntervalController extends Controller
{
    public function index()
    {
        $interval = $this->getModel('Interval');

        $data = $interval->getAll();

        echo $this->twig->render('index.twig', [
            'ranges' => $data,
        ]);

    }

    public function getOne($id)
    {
        $header = $this->container->get(Header::class);
        $interval = $this->getModel('Interval');
        $data = $interval->getOne($id, Interval::FETCH_ARR);

        $data = [
            'status' => 'ok',
            'data' => $data,
        ];
        $header->send($data);
        return true;
    }

    public function getAll()
    {
        $header = $this->container->get(Header::class);
        $interval = $this->getModel('Interval');

        $data = $interval->getAll(Interval::FETCH_ARR);

        $data = [
            'status' => 'ok',
            'data' => $data,
        ];
        $header->send($data);
        return true;

    }


    public function add()
    {
        $startDate = $this->request->get('start_date');
        $endDate = $this->request->get('end_date');
        $price = $this->request->get('price');

        $header = $this->container->get(Header::class);
        $fields = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price' => $price,
        ];

        $validator = (new ValidatorFactory())->make($fields, [
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'price'      => 'required|numeric|min:0.01'
        ] );

        if ($validator->fails()) {
            return $header->sendCode(['status'=>'error',
                'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        /**
         * @var $intervalModel \App\Models\Interval
         */
        $intervalModel = $this->getModel('Interval');
        $data = $intervalModel->getIntervals($startDate, $endDate);
//var_dump($data);
//die;

        $newInterval = new IntervalValue([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price' => $price
        ]);
//var_dump($newInterval);
//die;
        $dispatcher = new IntervalDispatcher($newInterval);

        foreach ($data as $id => $dbInterval) {
            $dispatcher->addInterval($dbInterval);
        }

        $intervals = $dispatcher->getIntervals();

var_dump($intervals);
        // TODO: loop through $intervals and insert/delete/update according to the $interval->getAction()

/*

        // TODO: refactor this hell to the command, strategy and chain of responsibility :)
        switch (count($data)) {
            case 0: // simple insert, no intersections
                $alg = new NoIntersections($intervalModel, $newInterval);
                $alg->doCalc();
                break;
            case 1: // left or right intersection OR inclusion
                reset($data);
                $dbInterval = current($data);

                if ($dbInterval->getIntersect() == 1) {
                    // 1 # NEW START-END range is between existing start-end range or identical

                    // checking if both start and end dates are identical then required to
                    // update existing interval with the new data from the new interval (in our case update price)
                    if($newInterval->getStartDate() == $dbInterval->getStartDate() && $newInterval->getEndDate() == $dbInterval->getEndDate()) {

                        $alg = new InnerStartEndIdentical($intervalModel, $dbInterval, $newInterval);
                        $alg->doCalc();

                    } // if start dates identical then adding new interval and updating existing interval start date
                      // with the new interval end date
                    elseif ($newInterval->getStartDate() == $dbInterval->getStartDate()) {

                        $alg = new InnerStartIdentical($intervalModel, $dbInterval, $newInterval);
                        $alg->doCalc();

                    } // if END dates identical then adding new interval and updating existing interval END date
                      // with the new interval start date
                    elseif ($newInterval->getEndDate() == $dbInterval->getEndDate()) {

                        $alg = new InnerEndIdentical($intervalModel, $dbInterval, $newInterval);
                        $alg->doCalc();

                    } // new interval somewhere between start and end of the existing interval
                      // in this case we have 2 new intervals to add and one existing to modify
                    else {
                        $alg = new BetweenStartEnd($intervalModel, $dbInterval, $newInterval);
                        $alg->doCalc();

                    }
                } elseif ($dbInterval->getIntersect() == 2) { // 2 # NEW START intersect or equal existing END
                    $alg = new OuterStartEndIntersect($intervalModel, $dbInterval, $newInterval);
                    $alg->doCalc();
                } elseif ($dbInterval->getIntersect() == 3) { // 3 # NEW START JOINS existing END
                    $alg = new OuterStartNearEnd($intervalModel, $dbInterval, $newInterval);
                    $alg->doCalc();
                } elseif ($dbInterval->getIntersect() == 4) { // 4 # NEW END intersect or equal existing START
                    $alg = new OuterEndStartIntersect($intervalModel, $dbInterval, $newInterval);
                    $alg->doCalc();
                } elseif ($dbInterval->getIntersect() == 5) { // 5 # NEW END JOINS existing START
                    $alg = new OuterEndNearStart($intervalModel, $dbInterval, $newInterval);
                    $alg->doCalc();
                } elseif ($dbInterval->getIntersect() == 6) { // 6 # NEW START-END range is wide and include already existing ranges (even few)
                    // delete all existing intervals and insert NEW interval
                    $alg = new WideStartEnd($intervalModel, $newInterval);
                    $alg->doCalc();
                }
                break;
            case 2: // both sides intersection

                break;
            default: // NEW range is wide and include one or more existing ranges
                // delete all existing intervals and insert NEW interval
                $alg = new WideStartEnd($intervalModel, $newInterval);
                $alg->doCalc();

        }
*/
        /*
        $data = [
            'status' => 'ok',
            'data' => $data,
        ];
        $header->send($data);
*/
    }
}