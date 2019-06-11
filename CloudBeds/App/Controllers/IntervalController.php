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
        $interval = $this->getModel('Interval');
        $data = $interval->getIntervals($startDate, $endDate);
var_dump($data[0]->toArray());

        $newInterval = new IntervalValue([
//            'id' => 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price' => $price
        ]);

        switch (count($data)) {
            case 0: // simple insert, no intersections

                $interval = $this->getModel('Interval');
                $interval->add($newInterval);
                break;
            case 1: // left or right intersection OR inclusion

                if ($data[0]->getIntersect() == 1){ // inner inclusion
                    echo 'hello';
                }

                break;
            case 2: // both sides intersection

                break;
            default: // NEW range is wide and include one or more existing ranges
                // delete all rows from $data
                // insert $newInterval

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