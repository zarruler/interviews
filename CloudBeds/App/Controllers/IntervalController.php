<?php
namespace App\Controllers;

use App\Models\Interval;
use Core\Config\ValidatorFactory;
use Core\Controller;
use Core\Database\IntervalValue;
use Core\Header;
use Core\Model;
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
         * @var $interval \App\Models\Interval
         */
        $intervalModel = $this->getModel('Interval');
        $data = $intervalModel->getIntervals($startDate, $endDate);
//var_dump($data);

        $newInterval = new IntervalValue([
//            'id' => 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price' => $price
        ]);

        // TODO: refactor this hell to the command, strategy and chain of responsibility :)
        switch (count($data)) {
            case 0: // simple insert, no intersections

                $intervalModel = $this->getModel('Interval');
                $intervalModel->add($newInterval);
                break;
            case 1: // left or right intersection OR inclusion
                reset($data);
                $dbInterval = current($data);

                if ($dbInterval->getIntersect() == 1) {       // 1 # NEW START-END range is between existing start-end range or identical
                    if ($dbInterval->getPrice() == $newInterval->getPrice()) {
                         // do nothing
                    } else { // start algorithm
                        // checking if both start and end dates are identical then required to
                        // update existing interval with the new data from the new interval (in our case update price)
                        if($newInterval->getStartDate() == $dbInterval->getStartDate() && $newInterval->getEndDate() == $dbInterval->getEndDate()) {

                            $dbInterval->setPrice($newInterval->getPrice());
                            $intervalModel->edit($dbInterval);
                            echo 'done';

                        } // if start dates identical then adding new interval and updating existing interval start date
                          // with the new interval end date
                        elseif ($newInterval->getStartDate() == $dbInterval->getStartDate()) {

                            $intervalModel->add($newInterval);

                            $updStartDate = $newInterval->getEndDate()->add(new \DateInterval('P1D'));
                            $dbInterval->setStartDate($updStartDate);
                            $intervalModel->edit($dbInterval);
                        } // if END dates identical then adding new interval and updating existing interval END date
                          // with the new interval start date
                        elseif ($newInterval->getEndDate() == $dbInterval->getEndDate()) {

                            $intervalModel->add($newInterval);

                            $updEndDate = $newInterval->getStartDate()->sub(new \DateInterval('P1D'));
                            $dbInterval->setEndDate($updEndDate);
                            $intervalModel->edit($dbInterval);

                        } // new interval somewhere between start and end of the existing interval
                          // in this case we have 2 new intervals to add and one existing to modify
                        else {
                            // ORDER IS IMPORTANT !!!
                            // firstly adding new interval
                            $intervalModel->add($newInterval);

                            // then creating another new interval based on first new interval END_date+1 as start date and
                            // existing interval END date as end date
                            $updStartDate = $newInterval->getEndDate()->add(new \DateInterval('P1D'));
                            $secondNewInterval = new IntervalValue([
                                'start_date' => $updStartDate,
                                'end_date' => $dbInterval->getEndDate(),
                                'price' => $dbInterval->getPrice()
                            ]);
                            $intervalModel->add($secondNewInterval);

                            // updating existing interval
                            $updEndDate = $newInterval->getStartDate()->sub(new \DateInterval('P1D'));
                            $dbInterval->setEndDate($updEndDate);
                            $intervalModel->edit($dbInterval);

                        }
                    }

                } elseif ($dbInterval->getIntersect() == 2) { // 2 # NEW START intersect or equal existing END
                    if ($dbInterval->getPrice() == $newInterval->getPrice()) {
                        // then intervals joined. update existing interval end with the end from new interval
                        // remaining existing interval start
                        $dbInterval->setEndDate($newInterval->getEndDate());
                        $intervalModel->edit($dbInterval);
                    } else {
                        // if prices different, then inserting new interval and updating existing END with the NEW_start-1
                        $intervalModel->add($newInterval);

                        $updEndDate = $newInterval->getStartDate()->sub(new \DateInterval('P1D'));
                        $dbInterval->setEndDate($updEndDate);
                        $intervalModel->edit($dbInterval);
                    }
                } elseif ($dbInterval->getIntersect() == 3) { // 3 # NEW START JOINS existing END
                    if ($dbInterval->getPrice() == $newInterval->getPrice()) {
                        // expanding existing range. existing END update with the new END
                        $dbInterval->setEndDate($newInterval->getEndDate());
                        $intervalModel->edit($dbInterval);
                    } else { // just add new interval
                        $intervalModel->add($newInterval);
                    }
                } elseif ($dbInterval->getIntersect() == 4) { // 4 # NEW END intersect or equal existing START
                    if ($dbInterval->getPrice() == $newInterval->getPrice()) {
                        // then intervals joined. update existing interval START with the START from new interval
                        // remaining existing interval END
                        $dbInterval->setStartDate($newInterval->getStartDate());
                        $intervalModel->edit($dbInterval);
                    } else {
                        // if prices different, then inserting new interval and updating existing START with the NEW_end+1
                        $intervalModel->add($newInterval);

                        $updEndDate = $newInterval->getEndDate()->add(new \DateInterval('P1D'));
                        $dbInterval->setStartDate($updEndDate);
                        $intervalModel->edit($dbInterval);
                    }
                } elseif ($dbInterval->getIntersect() == 5) { // 5 # NEW END JOINS existing START
                    if ($dbInterval->getPrice() == $newInterval->getPrice()) {
                        // expanding existing range. existing START update with the new START
                        $dbInterval->setStartDate($newInterval->getStartDate());
                        $intervalModel->edit($dbInterval);
                    } else {// just add new interval
                        $intervalModel->add($newInterval);
                    }
                } elseif ($dbInterval->getIntersect() == 6) { // 6 # NEW START-END range is wide and include already existing ranges (even few)
                    // delete all existing intervals and insert NEW interval
                    $ids = [];
                    foreach ($data as $key => $databaseInterval) {
                        $ids[] = $databaseInterval->getId();
                    }

                    if ($intervalModel->delete($ids) > 0)
                        $intervalModel->add($newInterval);
                }
                break;
            case 2: // both sides intersection

                break;
            default: // NEW range is wide and include one or more existing ranges
                // delete all existing intervals and insert NEW interval
                $ids = [];
                foreach ($data as $key => $databaseInterval) {
                    $ids[] = $databaseInterval->getId();
                }

                if ($intervalModel->delete($ids) > 0)
                    $intervalModel->add($newInterval);

        }
/*
        $data = [
            'status' => 'ok',
            'data' => $data,
        ];
        $header->send($data);
*/
    }
}